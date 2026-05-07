<?php

namespace App\Services;

use App\Support\DemoRepository;
use Illuminate\Support\Facades\Http;
use Throwable;

class PrintfulService
{
    public function __construct(protected DemoRepository $repository)
    {
    }

    public function syncProducts(): array
    {
        $apiKey = trim((string) env('PRINTFUL_API_KEY', ''));

        if ($apiKey === '') {
            return [
                'ok' => false,
                'message' => 'Printful API key is missing. Add PRINTFUL_API_KEY to .env, then press Sync again.',
                'products' => $this->repository->printfulProducts(),
            ];
        }

        try {
            $response = $this->printfulRequest($apiKey)
                ->get('https://api.printful.com/store/products');

            if (! $response->successful()) {
                return [
                    'ok' => false,
                    'message' => 'Printful sync failed: '.$response->status().' '.$response->body(),
                    'products' => $this->repository->printfulProducts(),
                ];
            }

            $rows = $response->json('result') ?? [];
            $products = collect($rows)->map(function ($row) use ($apiKey) {
                $syncProduct = $row['sync_product'] ?? $row;
                $productId = (string) ($syncProduct['id'] ?? $row['id'] ?? '');
                $detail = $productId !== '' ? $this->fetchPrintfulProductDetail($apiKey, $productId) : [];
                $fullPayload = is_array($detail) && $detail !== [] ? array_merge($row, $detail) : $row;
                $fullSyncProduct = $fullPayload['sync_product'] ?? $syncProduct;
                $variants = $this->extractPrintfulVariants($fullPayload);
                $defaultVariantId = (string) ($variants[0]['id'] ?? $variants[0]['sync_variant_id'] ?? $productId);

                return [
                    'id' => $productId,
                    'name' => (string) ($fullSyncProduct['name'] ?? $syncProduct['name'] ?? $row['name'] ?? 'Printful product'),
                    'thumbnail_url' => (string) ($fullSyncProduct['thumbnail_url'] ?? $fullSyncProduct['thumbnail'] ?? $syncProduct['thumbnail_url'] ?? $syncProduct['thumbnail'] ?? ''),
                    'description' => trim((string) ($fullSyncProduct['description'] ?? $row['description'] ?? '')),
                    'price' => $this->resolvePrintfulPrice($fullPayload),
                    'synced_at' => now()->toDateTimeString(),
                    'status' => (string) ($fullSyncProduct['status'] ?? 'synced'),
                    'default_variant_id' => $defaultVariantId,
                    'variants' => $variants,
                ];
            })->filter(fn ($product) => $product['id'] !== '')->values()->all();

            $this->repository->savePrintfulProducts($products);

            return [
                'ok' => true,
                'message' => count($products).' Printful product(s) synced successfully.',
                'products' => $products,
            ];
        } catch (Throwable $exception) {
            return [
                'ok' => false,
                'message' => $this->friendlyPrintfulError($exception),
                'products' => $this->repository->printfulProducts(),
            ];
        }
    }

    protected function printfulRequest(string $apiKey): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(30)
            ->retry(2, 500)
            ->withOptions([
                // Keep SSL verification enabled on production hosting.
                // Local Windows/PHP installs often miss a CA bundle, which causes cURL error 60.
                // For local testing only, set PRINTFUL_SSL_VERIFY=false in .env.
                'verify' => $this->shouldVerifySsl(),
            ]);
    }

    protected function shouldVerifySsl(): bool
    {
        $default = app()->environment('production');
        return filter_var(env('PRINTFUL_SSL_VERIFY', $default ? 'true' : 'false'), FILTER_VALIDATE_BOOLEAN);
    }

    protected function friendlyPrintfulError(Throwable $exception): string
    {
        $message = $exception->getMessage();

        if (str_contains($message, 'cURL error 60') || str_contains($message, 'unable to get local issuer certificate')) {
            return 'Printful SSL certificate problem on this local computer. I added the local-dev fix: set PRINTFUL_SSL_VERIFY=false in your .env, then run php artisan config:clear and press Sync again. On real hosting keep PRINTFUL_SSL_VERIFY=true.';
        }

        return 'Printful sync error: '.$message;
    }

    public function createOrder(array $order): array
    {
        $apiKey = trim((string) env('PRINTFUL_API_KEY', ''));

        if ($apiKey === '') {
            return ['ok' => false, 'message' => 'Printful API key missing. Order saved locally only.'];
        }

        try {
            // Extract printful items from the order
            $printfulItems = array_values(array_filter(
                $order['items'] ?? [],
                fn ($item) => ($item['source'] ?? '') === 'printful'
            ));

            if (empty($printfulItems)) {
                return ['ok' => true, 'message' => 'No Printful items in this order.'];
            }

            // Build the Printful order payload
            $customer = $order['customer'] ?? [];
            $payloadItems = [];

            foreach ($printfulItems as $item) {
                $printfulProduct = $this->repository->findPrintfulProduct((string) ($item['id'] ?? $item['slug'] ?? ''));
                $syncVariantId = (int) ($item['sync_variant_id'] ?? 0);

                if ($syncVariantId <= 0 && $printfulProduct) {
                    $syncVariantId = (int) ($printfulProduct['default_variant_id'] ?? 0);
                }

                if ($syncVariantId <= 0) {
                    return [
                        'ok' => false,
                        'message' => 'Missing Printful variant ID for '.$item['name'].'. Sync Printful products again from the admin panel.',
                    ];
                }

                $payloadItems[] = [
                    'sync_variant_id' => $syncVariantId,
                    'quantity' => 1,
                    'retail_price' => $this->extractPrice((string) ($item['price'] ?? '0')),
                ];
            }

            // Create the Printful order
            $payload = [
                'recipient' => [
                    'name' => trim((string) ($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')),
                    'address1' => (string) ($customer['address'] ?? ''),
                    'city' => (string) ($customer['city'] ?? ''),
                    'state_code' => '', // Printful may require state for some countries
                    'country_code' => $this->getCountryCode((string) ($customer['country'] ?? '')),
                    'zip' => (string) ($customer['postal_code'] ?? ''),
                    'email' => (string) ($customer['email'] ?? ''),
                    'phone' => (string) ($customer['phone'] ?? ''),
                ],
                'items' => $payloadItems,
                'shipping' => 'STANDARD', // Standard shipping
                'production_time_days' => 5,
            ];

            $response = $this->printfulRequest($apiKey)
                ->post('https://api.printful.com/orders', $payload);

            if (!$response->successful()) {
                return [
                    'ok' => false,
                    'message' => 'Printful order creation failed: ' . $response->status() . ' - ' . ($response->json('error.message') ?? $response->body()),
                ];
            }

            $result = $response->json('result');
            return [
                'ok' => true,
                'message' => 'Order successfully sent to Printful for fulfillment.',
                'printful_order_id' => $result['id'] ?? '',
                'printful_status' => $result['status'] ?? '',
            ];
        } catch (Throwable $exception) {
            return [
                'ok' => false,
                'message' => 'Error creating Printful order: ' . $exception->getMessage(),
            ];
        }
    }

    protected function extractPrice(string $priceString): float
    {
        preg_match('/([0-9]+(?:[\.,][0-9]{1,2})?)/', $priceString, $matches);
        if (isset($matches[1])) {
            return (float) str_replace(',', '.', $matches[1]);
        }
        return 0.0;
    }

    protected function fetchPrintfulProductDetail(string $apiKey, string $productId): array
    {
        try {
            $response = $this->printfulRequest($apiKey)
                ->get('https://api.printful.com/store/products/'.$productId);

            if ($response->successful()) {
                $result = $response->json('result');
                return is_array($result) ? $result : [];
            }
        } catch (Throwable) {
            // Keep the list response if the detail endpoint fails.
        }

        return [];
    }

    protected function resolvePrintfulPrice(array $payload): string
    {
        $prices = [];
        $currency = strtoupper(trim((string) ($payload['currency'] ?? $payload['sync_product']['currency'] ?? env('STORE_CURRENCY', 'USD'))));

        foreach ($this->extractPrintfulVariants($payload) as $variant) {
            foreach (['retail_price', 'price', 'product_price'] as $key) {
                if (isset($variant[$key]) && is_scalar($variant[$key]) && trim((string) $variant[$key]) !== '') {
                    $prices[] = [trim((string) $variant[$key]), strtoupper(trim((string) ($variant['currency'] ?? $currency)))];
                    break;
                }
            }
        }

        foreach (['retail_price', 'price', 'product_price'] as $key) {
            if (isset($payload[$key]) && is_scalar($payload[$key]) && trim((string) $payload[$key]) !== '') {
                $prices[] = [trim((string) $payload[$key]), $currency];
            }
        }

        $formatted = array_values(array_unique(array_filter(array_map(
            fn ($entry) => $this->formatPrintfulPrice((string) $entry[0], (string) $entry[1]),
            $prices
        ))));

        if (count($formatted) === 1) {
            return $formatted[0];
        }

        if (count($formatted) > 1) {
            $numeric = [];
            foreach ($prices as $entry) {
                $value = $this->numericPrice((string) $entry[0]);
                if ($value !== null) {
                    $numeric[] = [$value, (string) $entry[1]];
                }
            }

            if ($numeric !== []) {
                usort($numeric, fn ($a, $b) => $a[0] <=> $b[0]);
                $min = $this->formatPrintfulPrice((string) $numeric[0][0], (string) $numeric[0][1]);
                $max = $this->formatPrintfulPrice((string) $numeric[count($numeric) - 1][0], (string) $numeric[count($numeric) - 1][1]);
                return $min === $max ? $min : $min.' - '.$max;
            }

            return $formatted[0];
        }

        return 'Resync for exact price';
    }

    protected function extractPrintfulVariants(array $payload): array
    {
        $variantSets = [
            $payload['sync_variants'] ?? null,
            $payload['variants'] ?? null,
            $payload['sync_product']['sync_variants'] ?? null,
        ];

        foreach ($variantSets as $variantSet) {
            if (! is_array($variantSet) || $variantSet === []) {
                continue;
            }

            return array_values(array_filter(array_map(function ($variant) {
                if (! is_array($variant)) {
                    return null;
                }

                return [
                    'id' => (string) ($variant['id'] ?? $variant['sync_variant_id'] ?? ''),
                    'name' => (string) ($variant['name'] ?? $variant['variant_name'] ?? 'Default'),
                    'retail_price' => (string) ($variant['retail_price'] ?? $variant['price'] ?? $variant['product_price'] ?? ''),
                    'currency' => strtoupper((string) ($variant['currency'] ?? env('STORE_CURRENCY', 'USD'))),
                ];
            }, $variantSet)));
        }

        return [];
    }

    protected function formatPrintfulPrice(string $value, string $currency = 'USD'): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (preg_match('/[^0-9.,]/', $value)) {
            return $value;
        }

        $numeric = $this->numericPrice($value);
        if ($numeric === null) {
            return $value;
        }

        $currency = strtoupper(trim($currency ?: env('STORE_CURRENCY', 'USD')));
        $symbol = match ($currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'MAD', 'DH', 'DHS' => 'DH ',
            default => '',
        };

        $formatted = number_format($numeric, 2, '.', '');
        return $symbol !== '' ? $symbol.$formatted : $formatted.' '.$currency;
    }

    protected function numericPrice(string $value): ?float
    {
        if (preg_match('/([0-9]+(?:[\.,][0-9]{1,2})?)/', $value, $matches)) {
            return (float) str_replace(',', '.', $matches[1]);
        }

        return null;
    }

    protected function getCountryCode(string $country): string
    {
        // Common country codes - expand as needed
        $countryCodes = [
            'morocco' => 'MA',
            'united states' => 'US',
            'canada' => 'CA',
            'united kingdom' => 'GB',
            'france' => 'FR',
            'germany' => 'DE',
            'spain' => 'ES',
            'italy' => 'IT',
            'netherlands' => 'NL',
            'belgium' => 'BE',
            'switzerland' => 'CH',
            'australia' => 'AU',
            'new zealand' => 'NZ',
        ];

        $countryLower = mb_strtolower(trim($country));
        return $countryCodes[$countryLower] ?? 'US';
    }
}
