<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('type')->toString();
        $products = Catalog::all();

        if (in_array($filter, ['Software', 'Service'], true)) {
            $products = array_values(array_filter($products, fn ($item) => $item['type'] === $filter));
        }

        return view('products.index', [
            'products' => $products,
            'filter' => $filter,
        ]);
    }

    public function show(string $slug): View
    {
        $product = Catalog::find($slug);

        abort_if($product === null, 404);

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => Catalog::related($product),
            'whatsAppUrl' => 'https://wa.me/'.env('WHATSAPP_NUMBER', '212640611520').'?text='.rawurlencode('Hello Afayar, I want more information about '.$product['name'].'.'),
        ]);
    }

    public function search(Request $request): View
    {
        $query = $request->string('q')->toString();

        return view('search.results', [
            'query' => $query,
            'results' => Catalog::search($query),
        ]);
    }

    public function live(Request $request): JsonResponse
    {
        $query = trim($request->string('q')->toString());
        $results = array_slice(Catalog::search($query), 0, 5);

        return response()->json([
            'query' => $query,
            'results' => array_map(function (array $product) {
                return [
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'short' => $product['short'],
                    'price_label' => $product['price_label'],
                    'image' => asset($product['image']),
                    'url' => route('products.show', $product['slug']),
                ];
            }, $results),
            'results_count' => count(Catalog::search($query)),
            'view_all_url' => route('search', ['q' => $query]),
            'products_url' => route('products.index'),
        ]);
    }
}
