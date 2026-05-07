<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class StripeCheckoutService
{
    public function createSession(array $order, string $successUrl, string $cancelUrl): array
    {
        $secret = trim((string) env('STRIPE_SECRET_KEY', ''));

        if ($secret === '') {
            return ['ok' => false, 'message' => 'Stripe secret key missing. Order saved locally only.'];
        }

        $lineItems = [];
        foreach ($order['items'] as $item) {
            $amount = $this->priceToCents($item['price'] ?? $item['display_price'] ?? '0');
            if ($amount <= 0) {
                $amount = 100; // Keeps Stripe from rejecting custom quote items. Update real prices before launch.
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower(env('STORE_CURRENCY', 'usd')),
                    'product_data' => ['name' => (string) ($item['name'] ?? 'Claire Stefanich Arts item')],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ];
        }

        try {
            $response = Http::asForm()
                ->withToken($secret)
                ->timeout(20)
                ->post('https://api.stripe.com/v1/checkout/sessions', array_merge([
                    'mode' => 'payment',
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'customer_email' => $order['customer']['email'] ?? null,
                    'metadata[order_id]' => $order['id'],
                ], $this->flattenLineItems($lineItems)));

            if (! $response->successful()) {
                return ['ok' => false, 'message' => 'Stripe session failed: '.$response->status().' '.$response->body()];
            }

            return [
                'ok' => true,
                'id' => $response->json('id'),
                'url' => $response->json('url'),
            ];
        } catch (Throwable $exception) {
            return ['ok' => false, 'message' => 'Stripe error: '.$exception->getMessage()];
        }
    }

    public function priceToCents(string|int|float $price): int
    {
        if (is_numeric($price)) {
            return (int) round(((float) $price) * 100);
        }

        preg_match('/([0-9]+(?:[\.,][0-9]{1,2})?)/', (string) $price, $matches);
        if (! isset($matches[1])) {
            return 0;
        }

        return (int) round(((float) str_replace(',', '.', $matches[1])) * 100);
    }

    protected function flattenLineItems(array $lineItems): array
    {
        $payload = [];

        foreach ($lineItems as $index => $item) {
            $payload["line_items[$index][quantity]"] = $item['quantity'];
            $payload["line_items[$index][price_data][currency]"] = $item['price_data']['currency'];
            $payload["line_items[$index][price_data][unit_amount]"] = $item['price_data']['unit_amount'];
            $payload["line_items[$index][price_data][product_data][name]"] = $item['price_data']['product_data']['name'];
        }

        return $payload;
    }
}
