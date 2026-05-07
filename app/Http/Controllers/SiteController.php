<?php

namespace App\Http\Controllers;

use App\Support\DemoRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function __construct(protected DemoRepository $repository)
    {
    }

    public function home(): View
    {
        $products = $this->repository->products();
        $settings = $this->repository->settings();
        $heroProductImages = array_values(array_unique(array_filter(array_map(function ($product) {
            return $product['images'][0] ?? null;
        }, $this->repository->productsByCategory('all')))));

        // Get the last 3 printful products for the prints section
        $allPrintfulProducts = $this->repository->printfulAsProducts();
        $lastPrintfulProducts = array_slice($allPrintfulProducts, max(0, count($allPrintfulProducts) - 3), 3);
        $lastPrintfulProducts = array_reverse($lastPrintfulProducts); // Show newest first

        return view('pages.home', [
            'products' => $products,
            'latestOriginals' => $this->repository->latestOriginals(4),
            'commissionExamples' => $this->repository->commissionExamples(3),
            'printfulProducts' => $lastPrintfulProducts,
            'heroProductImages' => $heroProductImages,
            'featuredTestimonial' => $this->repository->featuredTestimonial(),
            'instagramUrl' => $settings['instagram_url'],
            'tiktokUrl' => $settings['tiktok_url'],
        ]);
    }

    public function products(Request $request): View
    {
        $category = $request->string('category')->toString() ?: 'all';
        $allProducts = $this->repository->productsByCategory($category);
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 6;
        $items = array_slice($allProducts, ($page - 1) * $perPage, $perPage);

        $categoryLabels = [
            'all' => 'All products',
            'sold' => 'Sold products',
            'original' => 'Originals',
        ];

        $paginated = new LengthAwarePaginator($items, count($allProducts), $perPage, $page, [
            'path' => route('products.index'),
            'query' => $request->query(),
        ]);

        return view('pages.products', [
            'products' => $paginated,
            'category' => $category,
            'categoryLabels' => $categoryLabels,
        ]);
    }

    public function product(string $slug): View
    {
        $product = $this->repository->findProduct($slug);
        abort_unless($product !== null, 404);

        $related = array_values(array_filter(
            $this->repository->productsByCategory($product['category']),
            fn ($item) => $item['slug'] !== $slug && $item['category'] === $product['category']
        ));

        return view('pages.product', [
            'product' => $product,
            'related' => $related,
            'reviews' => $this->repository->testimonials(),
        ]);
    }

    public function prints(): View
    {
        return view('pages.prints', [
            'printfulProducts' => $this->repository->printfulAsProducts(),
            'settings' => $this->repository->settings(),
        ]);
    }

    public function commissions(): View
    {
        return view('pages.commissions', [
            'commissionExamples' => $this->repository->commissionExamples(9),
        ]);
    }

    public function testimonials(): View
    {
        return view('pages.testimonials', [
            'testimonials' => $this->repository->testimonials(),
        ]);
    }

    public function about(): View
    {
        $settings = $this->repository->settings();

        return view('pages.about', [
            'products' => $this->repository->latestOriginals(3),
            'instagramUrl' => $settings['instagram_url'],
            'tiktokUrl' => $settings['tiktok_url'],
            'email' => $settings['store_email'],
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120'],
            'subject' => ['nullable', 'string', 'max:160'],
            'commission_type' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $this->repository->addMessage($data);

        return back()->with('success', 'Your message was sent successfully.');
    }

    public function commissionSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120'],
            'commission_type' => ['required', 'string', 'max:120'],
            'budget' => ['nullable', 'string', 'max:120'],
            'deadline' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $this->repository->addMessage(array_merge($data, [
            'subject' => 'Commission request',
        ]));

        return back()->with('success', 'Commission request sent successfully.');
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
        ]);

        $this->repository->addSubscriber($data);

        return back()->with('success', 'You joined Claire’s email list successfully.');
    }

    public function orderInquiry(Request $request, string $slug): RedirectResponse
    {
        $product = $this->repository->findProduct($slug);
        abort_unless($product !== null, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->repository->addOrder([
            'customer' => [
                'first_name' => $data['name'],
                'last_name' => '',
                'email' => $data['email'],
                'message' => $data['message'] ?? '',
            ],
            'items' => [[
                'name' => $product['name'],
                'price' => $product['price'],
                'type' => $product['category'],
                'source' => 'product inquiry',
            ]],
            'subtotal' => 0,
            'payment_status' => 'inquiry',
            'fulfillment_status' => 'not_required',
        ]);

        return back()->with('success', 'Your interest has been recorded. Claire can follow up by email.');
    }
}
