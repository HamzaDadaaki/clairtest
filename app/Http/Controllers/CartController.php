<?php

namespace App\Http\Controllers;

use App\Support\DemoRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected DemoRepository $repository)
    {
    }

    public function index(Request $request): View
    {
        $items = $this->cartItems($request);

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $this->calculateSubtotal($items),
        ]);
    }

    public function add(Request $request, string $slug): RedirectResponse
    {
        abort_unless($this->repository->findProduct($slug) !== null, 404);

        $cart = $request->session()->get('cart', []);
        if (! in_array($slug, $cart, true)) {
            $cart[] = $slug;
        }
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Artwork added to cart.');
    }

    public function addPrintful(Request $request, string $id): RedirectResponse
    {
        abort_unless($this->repository->findPrintfulProduct($id) !== null, 404);

        $cart = $request->session()->get('printful_cart', []);
        if (! in_array((string) $id, $cart, true)) {
            $cart[] = (string) $id;
        }
        $request->session()->put('printful_cart', $cart);

        return back()->with('success', 'Print added to cart.');
    }

    public function remove(Request $request, string $slug): RedirectResponse
    {
        $request->session()->put('cart', array_values(array_filter(
            $request->session()->get('cart', []),
            fn ($item) => $item !== $slug
        )));

        $request->session()->put('printful_cart', array_values(array_filter(
            $request->session()->get('printful_cart', []),
            fn ($item) => $item !== $slug
        )));

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget(['cart', 'printful_cart']);

        return back()->with('success', 'Cart cleared.');
    }

    public function cartItems(Request $request): array
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
                'url' => route('products.show', $product['slug']),
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
                'url' => route('products.show', 'print-'.\Illuminate\Support\Str::slug((string) $product['id'])),
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
}
