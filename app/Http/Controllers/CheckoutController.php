<?php

namespace App\Http\Controllers;

use App\Services\PrintfulService;
use App\Services\StripeCheckoutService;
use App\Support\DemoRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected DemoRepository $repository,
        protected StripeCheckoutService $stripe,
        protected PrintfulService $printful,
    ) {
    }

    public function show(Request $request): View
    {
        $items = $this->cartItems($request);

        if (empty($items)) {
            abort(404, 'Your cart is empty');
        }

        return view('cart.checkout', [
            'items' => $items,
            'subtotal' => $this->calculateSubtotal($items),
            'stripeReady' => (bool) env('STRIPE_SECRET_KEY'),
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:40',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:40',
            'country' => 'required|string|max:100',
            'message' => 'nullable|string|max:1000',
        ]);

        $items = $this->cartItems($request);
        if (empty($items)) {
            return back()->with('error', 'Your cart is empty.');
        }

        $order = $this->repository->addOrder([
            'customer' => $validated,
            'items' => $items,
            'subtotal' => $this->calculateSubtotal($items),
            'payment_status' => env('STRIPE_SECRET_KEY') ? 'pending_payment' : 'manual_payment_required',
            'fulfillment_status' => $this->hasPrintfulItems($items) ? 'waiting_payment' : 'not_required',
        ]);

        $request->session()->forget(['cart', 'printful_cart']);

        $stripeSession = $this->stripe->createSession(
            $order,
            route('checkout.success', ['orderId' => $order['id']]),
            route('checkout.show')
        );

        if ($stripeSession['ok'] ?? false) {
            $this->repository->updateOrder($order['id'], [
                'stripe_session_id' => $stripeSession['id'] ?? '',
            ]);

            return redirect()->away($stripeSession['url']);
        }

        return redirect()->route('checkout.confirmation', ['orderId' => $order['id']])
            ->with('success', 'Order saved in the admin panel. Add Stripe keys to enable live card payment.');
    }

    public function success(string $orderId): RedirectResponse
    {
        $order = $this->repository->updateOrder($orderId, [
            'payment_status' => 'paid',
            'fulfillment_status' => 'paid_waiting_printful',
        ]);

        if ($order && $this->hasPrintfulItems($order['items'])) {
            $result = $this->printful->createOrder($order);
            if ($result['ok'] ?? false) {
                $this->repository->updateOrder($orderId, [
                    'fulfillment_status' => 'sent_to_printful',
                    'printful_order_id' => $result['printful_order_id'] ?? '',
                ]);
            }
        }

        return redirect()->route('checkout.confirmation', ['orderId' => $orderId])
            ->with('success', 'Payment completed successfully.');
    }

    public function confirmation(string $orderId): View
    {
        $order = $this->repository->findOrder($orderId);
        abort_unless($order !== null, 404, 'Order not found');

        return view('cart.confirmation', [
            'order' => $order,
        ]);
    }

    protected function cartItems(Request $request): array
    {
        $localSlugs = $request->session()->get('cart', []);
        $printfulIds = $request->session()->get('printful_cart', []);

        $localItems = array_values(array_filter(array_map(function ($slug) {
            $product = $this->repository->findProduct($slug);
            if (! $product) {
                return null;
            }

            return [
                'id' => $product['slug'],
                'slug' => $product['slug'],
                'name' => $product['name'],
                'description' => $product['description'],
                'display_price' => $product['price'],
                'price' => $product['price'],
                'image' => $product['images'][0] ?? '',
                'type' => $product['category'],
                'source' => 'local',
            ];
        }, $localSlugs)));

        $printfulItems = array_values(array_filter(array_map(function ($id) {
            $product = $this->repository->findPrintfulProduct((string) $id);
            if (! $product) {
                return null;
            }

            return [
                'id' => (string) $product['id'],
                'slug' => (string) $product['id'],
                'name' => $product['name'],
                'description' => trim((string) ($product['description'] ?? 'Fine art print product.')),
                'display_price' => $product['price'],
                'price' => $product['price'],
                'image' => $product['thumbnail_url'],
                'type' => 'print',
                'source' => 'printful',
                'sync_variant_id' => $product['default_variant_id'] ?? $product['id'],
            ];
        }, $printfulIds)));

        return array_merge($localItems, $printfulItems);
    }

    protected function calculateSubtotal(array $items): float
    {
        return array_reduce($items, function ($carry, $item) {
            preg_match('/([0-9]+(?:[\.,][0-9]{1,2})?)/', (string) ($item['price'] ?? ''), $matches);
            return $carry + (isset($matches[1]) ? (float) str_replace(',', '.', $matches[1]) : 0);
        }, 0.0);
    }

    protected function hasPrintfulItems(array $items): bool
    {
        foreach ($items as $item) {
            if (($item['source'] ?? '') === 'printful') {
                return true;
            }
        }
        return false;
    }
}
