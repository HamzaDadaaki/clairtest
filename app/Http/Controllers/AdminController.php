<?php

namespace App\Http\Controllers;

use App\Services\PrintfulService;
use App\Support\DemoRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(protected DemoRepository $repository)
    {
    }

    protected function ensureAuth(): void
    {
        abort_unless(session('clara_admin') === true, 403);
    }

    public function login(): View
    {
        return view('pages.admin.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate(['password' => ['required', 'string']]);
        $password = env('CLARA_ADMIN_PASSWORD', 'claire2026');

        if ($data['password'] !== $password) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        session(['clara_admin' => true]);
        return redirect()->route('admin.dashboard');
    }

    public function logout(): RedirectResponse
    {
        session()->forget('clara_admin');
        return redirect()->route('admin.login');
    }

    public function dashboard(): View
    {
        $this->ensureAuth();

        return $this->dashboardView(null);
    }

    public function editProduct(string $slug): View
    {
        $this->ensureAuth();

        return $this->dashboardView($this->repository->findProduct($slug));
    }

    protected function dashboardView(?array $editing): View
    {
        $products = $this->repository->products();
        $orders = $this->repository->orders();
        $messages = $this->repository->messages();
        $subscribers = $this->repository->subscribers();
        $printfulProducts = $this->repository->printfulProducts();

        return view('pages.admin.dashboard', [
            'products' => $products,
            'messages' => $messages,
            'orders' => $orders,
            'editing' => $editing,
            'testimonials' => $this->repository->testimonials(),
            'subscribers' => $subscribers,
            'printfulProducts' => $printfulProducts,
            'settings' => $this->repository->settings(),
            'categoryLabels' => $this->repository->categoryLabels(),
            'statusLabels' => $this->repository->statusLabels(),
            'stats' => [
                'products' => count($products),
                'orders' => count($orders),
                'messages' => count($messages),
                'subscribers' => count($subscribers),
                'printful' => count($printfulProducts),
            ],
        ]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $this->ensureAuth();
        $this->repository->saveProduct($this->validatedProduct($request));
        return back()->with('success', 'Product created successfully.');
    }

    public function updateProduct(Request $request, string $slug): RedirectResponse
    {
        $this->ensureAuth();
        $existingProduct = $this->repository->findProduct($slug);
        abort_unless($existingProduct !== null, 404);

        $this->repository->saveProduct($this->validatedProduct($request, $existingProduct), $slug);
        return redirect()->route('admin.dashboard')->with('success', 'Product updated successfully.');
    }

    public function deleteProduct(string $slug): RedirectResponse
    {
        $this->ensureAuth();
        $this->repository->deleteProduct($slug);
        return back()->with('success', 'Product removed successfully.');
    }

    public function storeTestimonial(Request $request): RedirectResponse
    {
        $this->ensureAuth();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'context' => ['nullable', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:2000'],
            'featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:6144'],
        ]);

        if ($request->file('image') instanceof UploadedFile) {
            $data['image'] = $this->storeUploadedImage($request->file('image'));
        }

        $data['featured'] = $request->boolean('featured');
        $this->repository->saveTestimonial($data);

        return back()->with('success', 'Testimonial added successfully.');
    }

    public function deleteTestimonial(string $id): RedirectResponse
    {
        $this->ensureAuth();
        $this->repository->deleteTestimonial($id);
        return back()->with('success', 'Testimonial removed successfully.');
    }

    public function deleteSubscriber(string $id): RedirectResponse
    {
        $this->ensureAuth();
        $this->repository->deleteSubscriber($id);
        return back()->with('success', 'Subscriber removed successfully.');
    }

    public function exportSubscribers(): Response
    {
        $this->ensureAuth();

        $csv = "Name,Email,Date\n";
        foreach ($this->repository->subscribers() as $subscriber) {
            $csv .= '"'.str_replace('"', '""', $subscriber['name']).'","'.str_replace('"', '""', $subscriber['email']).'","'.$subscriber['created_at'].'"' . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="claire-email-subscribers.csv"',
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $this->ensureAuth();
        $data = $request->validate([
            'store_email' => ['required', 'email', 'max:160'],
            'currency' => ['required', 'string', 'max:10'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
        ]);

        $this->repository->saveSettings($data);
        return back()->with('success', 'Settings saved successfully.');
    }

    public function syncPrintful(PrintfulService $printful): RedirectResponse
    {
        $this->ensureAuth();
        $result = $printful->syncProducts();

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function updateOrder(Request $request, string $id): RedirectResponse
    {
        $this->ensureAuth();
        $data = $request->validate([
            'payment_status' => ['required', 'string', 'max:80'],
            'fulfillment_status' => ['required', 'string', 'max:80'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->repository->updateOrder($id, $data);
        return back()->with('success', 'Order status updated.');
    }

    public function deleteOrder(string $id): RedirectResponse
    {
        $this->ensureAuth();
        $this->repository->deleteOrder($id);

        return back()->with('success', 'Order removed successfully.');
    }

    protected function validatedProduct(Request $request, ?array $existingProduct = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'string', 'max:60'],
            'tag' => ['nullable', 'string', 'max:120'],
            'category' => ['required', 'string', 'in:original,print,commission_example'],
            'status' => ['required', 'string', 'in:for_sale,sold,reserved,hidden'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:3000'],
            'size' => ['nullable', 'string', 'max:120'],
            'story' => ['nullable', 'string', 'max:4000'],
            'featured' => ['nullable', 'boolean'],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*' => ['nullable', 'string', 'max:255'],
            'new_images' => ['nullable', 'array'],
            'new_images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:6144'],
        ]);

        $existingImages = collect($data['existing_images'] ?? [])
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '' && str_starts_with($item, 'assets/images/'));

        $uploadedImages = collect($request->file('new_images', []))
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->map(fn (UploadedFile $file) => $this->storeUploadedImage($file));

        $images = $existingImages->merge($uploadedImages)->unique()->values();

        if ($images->isEmpty() && $existingProduct !== null) {
            $images = collect($existingProduct['images'] ?? []);
        }

        if ($images->isEmpty()) {
            throw ValidationException::withMessages(['new_images' => 'Please upload at least one product image.']);
        }

        return [
            'name' => trim($data['name']),
            'price' => trim($data['price']),
            'tag' => trim((string) ($data['tag'] ?? 'Original artwork')),
            'category' => $data['category'],
            'status' => $data['status'],
            'button_label' => trim((string) ($data['button_label'] ?? 'Ask to buy')),
            'description' => trim($data['description']),
            'size' => trim((string) ($data['size'] ?? 'Available on request')),
            'story' => trim((string) ($data['story'] ?? '')),
            'images' => $images->all(),
            'featured' => $request->boolean('featured'),
        ];
    }

    protected function storeUploadedImage(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = $safeName !== '' ? $safeName : 'artwork';
        $filename = now()->format('YmdHis').'-'.Str::random(8).'-'.$safeName.'.'.$extension;
        $destination = public_path('assets/images/products/uploads');

        if (! is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $file->move($destination, $filename);

        return 'assets/images/products/uploads/'.$filename;
    }
}
