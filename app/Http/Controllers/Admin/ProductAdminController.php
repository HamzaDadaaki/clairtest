<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Catalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Catalog::all(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => $this->emptyProduct(),
            'allProducts' => Catalog::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedPayload($request);
        $slug = $payload['slug'];

        if (Catalog::slugExists($slug)) {
            return back()
                ->withInput()
                ->withErrors(['slug' => 'This slug already exists. Please choose another one.']);
        }

        $product = $this->buildProductPayload($request, $payload);
        Catalog::upsert($product);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(string $slug): View
    {
        $product = Catalog::find($slug);
        abort_if($product === null, 404);

        return view('admin.products.edit', [
            'product' => $product,
            'allProducts' => array_values(array_filter(Catalog::all(), fn ($item) => $item['slug'] !== $slug)),
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $existing = Catalog::find($slug);
        abort_if($existing === null, 404);

        $payload = $this->validatedPayload($request, $slug);
        $newSlug = $payload['slug'];

        if (Catalog::slugExists($newSlug, $slug)) {
            return back()
                ->withInput()
                ->withErrors(['slug' => 'This slug already exists. Please choose another one.']);
        }

        $product = $this->buildProductPayload($request, $payload, $existing);
        Catalog::upsert($product, $slug);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(string $slug): RedirectResponse
    {
        $product = Catalog::find($slug);
        abort_if($product === null, 404);

        Catalog::delete($slug);

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    protected function validatedPayload(Request $request, ?string $ignoreSlug = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180'],
            'type' => ['required', Rule::in(['Software', 'Service'])],
            'badge' => ['nullable', 'string', 'max:120'],
            'price_label' => ['nullable', 'string', 'max:120'],
            'short' => ['required', 'string', 'max:400'],
            'description' => ['required', 'string', 'max:4000'],
            'hero_note' => ['nullable', 'string', 'max:255'],
            'features_text' => ['nullable', 'string', 'max:4000'],
            'related_text' => ['nullable', 'string', 'max:1000'],
            'existing_gallery_text' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:6144'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:6144'],
            'async_uploaded_main_image' => ['nullable', 'string', 'max:255'],
            'async_uploaded_gallery' => ['nullable', 'array'],
            'async_uploaded_gallery.*' => ['nullable', 'string', 'max:255'],
            'review_name' => ['nullable', 'array'],
            'review_name.*' => ['nullable', 'string', 'max:120'],
            'review_role' => ['nullable', 'array'],
            'review_role.*' => ['nullable', 'string', 'max:120'],
            'review_text' => ['nullable', 'array'],
            'review_text.*' => ['nullable', 'string', 'max:1000'],
            'featured' => ['nullable', 'boolean'],
            'latest' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug(trim((string) ($validated['slug'] ?? ''))) ?: Str::slug($validated['name']);
        $validated['slug'] = $validated['slug'] !== '' ? $validated['slug'] : 'product-'.Str::lower(Str::random(8));

        return $validated;
    }

    protected function buildProductPayload(Request $request, array $validated, array $existing = []): array
    {
        $features = collect(preg_split('/\r\n|\r|\n/', (string) ($validated['features_text'] ?? '')))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();

        $related = collect(explode(',', (string) ($validated['related_text'] ?? '')))
            ->map(fn ($item) => Str::slug(trim((string) $item)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $existingGallery = collect(preg_split('/\r\n|\r|\n/', (string) ($validated['existing_gallery_text'] ?? '')))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();

        $mainImage = trim((string) ($validated['async_uploaded_main_image'] ?? ''));
        if ($mainImage === '') {
            $mainImage = $existing['image'] ?? '';
        }

        if ($request->hasFile('image')) {
            $mainImage = $this->storeUploadedImage($request->file('image'));
        }

        $galleryUploads = collect($validated['async_uploaded_gallery'] ?? [])
            ->filter(fn ($item) => is_string($item) && trim($item) !== '')
            ->map(fn ($item) => trim((string) $item))
            ->values();

        $galleryUploads = $galleryUploads->merge(
            collect($request->file('gallery_images', []))
                ->filter()
                ->map(fn ($file) => $this->storeUploadedImage($file))
        );

        $gallery = $existingGallery
            ->merge($galleryUploads)
            ->filter()
            ->unique()
            ->values();

        if ($mainImage !== '' && ! $gallery->contains($mainImage)) {
            $gallery->prepend($mainImage);
        }

        $reviews = [];
        $names = $request->input('review_name', []);
        $roles = $request->input('review_role', []);
        $texts = $request->input('review_text', []);
        $maxReviews = max(count($names), count($roles), count($texts));

        for ($i = 0; $i < $maxReviews; $i++) {
            $name = trim((string) ($names[$i] ?? ''));
            $role = trim((string) ($roles[$i] ?? ''));
            $text = trim((string) ($texts[$i] ?? ''));

            if ($name === '' && $role === '' && $text === '') {
                continue;
            }

            $reviews[] = [
                'name' => $name,
                'role' => $role,
                'text' => $text,
            ];
        }

        return [
            'slug' => $validated['slug'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'badge' => $validated['badge'] ?? '',
            'price_label' => $validated['price_label'] ?? '',
            'short' => $validated['short'],
            'description' => $validated['description'],
            'features' => $features,
            'review' => $reviews,
            'hero_note' => $validated['hero_note'] ?? '',
            'related' => $related,
            'featured' => $request->boolean('featured'),
            'latest' => $request->boolean('latest'),
            'gallery' => $gallery->all(),
            'image' => $mainImage !== '' ? $mainImage : ($gallery->first() ?? 'assets/images/products/afayar-pos.webp'),
        ];
    }

    protected function storeUploadedImage($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'webp');
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = $safeName !== '' ? $safeName : 'product-image';
        $filename = now()->format('YmdHis').'-'.Str::random(8).'-'.$safeName.'.'.$extension;

        $destination = public_path('assets/images/products/uploads');

        if (! is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $file->move($destination, $filename);

        return 'assets/images/products/uploads/'.$filename;
    }

    protected function emptyProduct(): array
    {
        return [
            'slug' => '',
            'name' => '',
            'type' => 'Software',
            'badge' => '',
            'price_label' => '',
            'short' => '',
            'description' => '',
            'features' => [],
            'review' => [
                ['name' => '', 'role' => '', 'text' => ''],
            ],
            'hero_note' => '',
            'related' => [],
            'featured' => false,
            'latest' => false,
            'gallery' => [],
            'image' => '',
        ];
    }
}
