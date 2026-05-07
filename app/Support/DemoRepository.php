<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DemoRepository
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = storage_path('app/demo');
        if (! File::exists($this->basePath)) {
            File::makeDirectory($this->basePath, 0755, true);
        }
        $this->seedDefaults();
    }

    public function products(): array
    {
        $products = $this->read('products.json');
        if ($products === []) {
            $products = $this->defaultProducts();
            $this->write('products.json', $products);
        }

        return array_values(array_map(fn (array $product) => $this->normalizeProduct($product), $products));
    }

    public function productsByCategory(?string $category = null): array
    {
        $products = $this->products();
        $printProducts = $this->printfulAsProducts();

        if (! $category || $category === 'all') {
            // Keep the shop focused on originals and sold pieces.
            return array_values(array_filter($products, fn ($product) => ! in_array($product['category'], ['commission_example', 'print'], true)));
        }

        if ($category === 'sold') {
            return array_values(array_filter($products, fn ($product) => $product['status'] === 'sold' && $product['category'] === 'original'));
        }

        if ($category === 'print') {
            return $printProducts;
        }

        // Exclude commission examples when filtering by other categories
        if ($category === 'original') {
            return array_values(array_filter($products, fn ($product) => $product['category'] === $category));
        }

        return array_values(array_filter($products, fn ($product) => $product['category'] === $category && $product['category'] !== 'commission_example'));
    }

    public function latestOriginals(int $limit = 3): array
    {
        return array_slice(array_values(array_filter(
            $this->products(),
            fn ($product) => $product['category'] === 'original' && $product['status'] !== 'sold'
        )), 0, $limit);
    }

    public function commissionExamples(int $limit = 6): array
    {
        return array_slice(array_values(array_filter(
            $this->products(),
            fn ($product) => $product['category'] === 'commission_example'
        )), 0, $limit);
    }

    public function findProduct(string $slug): ?array
    {
        foreach ($this->products() as $product) {
            if (($product['slug'] ?? null) === $slug) {
                return $product;
            }
        }

        foreach ($this->printfulAsProducts() as $product) {
            if (($product['slug'] ?? null) === $slug) {
                return $product;
            }
        }

        return null;
    }

    public function printfulAsProducts(): array
    {
        return array_values(array_map(function (array $product) {
            $id = (string) ($product['id'] ?? Str::uuid());
            $image = trim((string) ($product['thumbnail_url'] ?? ''));

            $normalized = $this->normalizeProduct([
                'name' => trim((string) ($product['name'] ?? 'Art print')),
                'slug' => 'print-'.Str::slug($id),
                'price' => $this->cleanPrintfulPriceLabel(trim((string) ($product['price'] ?? 'Resync for exact price'))),
                'tag' => 'Fine art print',
                'description' => trim((string) ($product['description'] ?? 'High quality art print available in selected formats.')),
                'size' => trim((string) ($product['size'] ?? 'Available in multiple sizes')),
                'story' => '',
                'status' => 'for_sale',
                'category' => 'print',
                'button_label' => 'View piece',
                'images' => $image !== '' ? [$image] : [],
                'featured' => true,
            ]);

            $normalized['printful_id'] = $id;
            $normalized['default_variant_id'] = trim((string) ($product['default_variant_id'] ?? $product['sync_variant_id'] ?? $id));
            $normalized['variants'] = is_array($product['variants'] ?? null) ? $product['variants'] : [];

            return $normalized;
        }, $this->printfulProducts()));
    }

    public function findPrintfulProduct(string $id): ?array
    {
        foreach ($this->printfulProducts() as $product) {
            if ((string) ($product['id'] ?? '') === (string) $id) {
                return $product;
            }
        }
        return null;
    }

    public function saveProduct(array $payload, ?string $originalSlug = null): void
    {
        $products = $this->products();
        $payload = $this->normalizeProduct($payload);
        $payload['slug'] = Str::slug($payload['name']);
        $updated = false;

        foreach ($products as $index => $product) {
            if (($originalSlug && $product['slug'] === $originalSlug) || $product['slug'] === $payload['slug']) {
                $products[$index] = $this->normalizeProduct(array_merge($product, $payload));
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            array_unshift($products, $payload);
        }

        $this->write('products.json', $products);
    }

    public function deleteProduct(string $slug): void
    {
        $filtered = array_values(array_filter($this->products(), fn ($product) => $product['slug'] !== $slug));
        $this->write('products.json', $filtered);
    }

    public function messages(): array
    {
        return array_values(array_map(fn (array $message) => $this->normalizeMessage($message), $this->read('messages.json')));
    }

    public function addMessage(array $payload): void
    {
        $messages = $this->messages();
        array_unshift($messages, $this->normalizeMessage(array_merge([
            'id' => (string) Str::uuid(),
            'created_at' => now()->toDateTimeString(),
            'status' => 'new',
        ], $payload)));
        $this->write('messages.json', $messages);
    }

    public function updateMessageStatus(string $id, string $status): void
    {
        $messages = $this->messages();
        foreach ($messages as $index => $message) {
            if ($message['id'] === $id) {
                $messages[$index]['status'] = in_array($status, ['new', 'in_progress', 'replied', 'archived'], true) ? $status : 'new';
            }
        }
        $this->write('messages.json', $messages);
    }

    public function orders(): array
    {
        return array_values(array_map(fn (array $order) => $this->normalizeOrder($order), $this->read('orders.json')));
    }

    public function addOrder(array $payload): array
    {
        $order = $this->normalizeOrder(array_merge([
            'id' => 'CSA-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
            'date' => now()->toDateTimeString(),
            'created_at' => now()->toDateTimeString(),
            'payment_status' => 'pending',
            'fulfillment_status' => 'not_sent',
        ], $payload));

        $orders = $this->orders();
        array_unshift($orders, $order);
        $this->write('orders.json', $orders);

        return $order;
    }

    public function updateOrder(string $id, array $changes): ?array
    {
        $orders = $this->orders();
        $updated = null;

        foreach ($orders as $index => $order) {
            if ($order['id'] === $id) {
                $orders[$index] = $this->normalizeOrder(array_merge($order, $changes, ['id' => $id]));
                $updated = $orders[$index];
                break;
            }
        }

        if ($updated) {
            $this->write('orders.json', $orders);
        }

        return $updated;
    }

    public function deleteOrder(string $id): void
    {
        $orders = array_values(array_filter($this->read('orders.json'), fn (array $order) => (string) ($order['id'] ?? '') !== $id));
        $this->write('orders.json', $orders);
    }

    public function findOrder(string $id): ?array
    {
        foreach ($this->orders() as $order) {
            if ($order['id'] === $id) {
                return $order;
            }
        }
        return null;
    }

    public function testimonials(): array
    {
        $testimonials = $this->read('testimonials.json');
        if ($testimonials === []) {
            $testimonials = $this->defaultTestimonials();
            $this->write('testimonials.json', $testimonials);
        }

        return array_values(array_map(fn (array $testimonial) => $this->normalizeTestimonial($testimonial), $testimonials));
    }

    public function featuredTestimonial(): ?array
    {
        foreach ($this->testimonials() as $testimonial) {
            if ($testimonial['featured']) {
                return $testimonial;
            }
        }

        return $this->testimonials()[0] ?? null;
    }

    public function saveTestimonial(array $payload, ?string $id = null): void
    {
        $testimonials = $this->testimonials();
        $payload = $this->normalizeTestimonial(array_merge(['id' => $id ?: (string) Str::uuid()], $payload));
        $updated = false;

        foreach ($testimonials as $index => $testimonial) {
            if ($testimonial['id'] === $payload['id']) {
                $testimonials[$index] = $payload;
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            array_unshift($testimonials, $payload);
        }

        $this->write('testimonials.json', $testimonials);
    }

    public function deleteTestimonial(string $id): void
    {
        $this->write('testimonials.json', array_values(array_filter($this->testimonials(), fn ($testimonial) => $testimonial['id'] !== $id)));
    }

    public function subscribers(): array
    {
        return array_values(array_map(fn (array $subscriber) => $this->normalizeSubscriber($subscriber), $this->read('subscribers.json')));
    }

    public function addSubscriber(array $payload): array
    {
        $subscriber = $this->normalizeSubscriber(array_merge([
            'id' => (string) Str::uuid(),
            'created_at' => now()->toDateTimeString(),
        ], $payload));

        $subscribers = array_values(array_filter($this->subscribers(), fn ($item) => mb_strtolower($item['email']) !== mb_strtolower($subscriber['email'])));
        array_unshift($subscribers, $subscriber);
        $this->write('subscribers.json', $subscribers);

        return $subscriber;
    }

    public function deleteSubscriber(string $id): void
    {
        $this->write('subscribers.json', array_values(array_filter($this->subscribers(), fn ($subscriber) => $subscriber['id'] !== $id)));
    }

    public function printfulProducts(): array
    {
        return array_values(array_map(fn (array $product) => $this->normalizePrintfulProduct($product), $this->read('printful_products.json')));
    }

    public function savePrintfulProducts(array $products): void
    {
        $this->write('printful_products.json', array_values(array_map(fn (array $product) => $this->normalizePrintfulProduct($product), $products)));
    }

    public function settings(): array
    {
        return array_merge([
            'stripe_status' => env('STRIPE_SECRET_KEY') ? 'configured' : 'missing_key',
            'printful_status' => env('PRINTFUL_API_KEY') ? 'configured' : 'missing_key',
            'store_email' => 'clairestefanichart@gmail.com',
            'currency' => env('STORE_CURRENCY', 'usd'),
            'instagram_url' => 'https://www.instagram.com/clairestefanich.art/',
            'tiktok_url' => 'https://www.tiktok.com/@clairestefanich.art',
        ], $this->read('settings.json'));
    }

    public function saveSettings(array $payload): void
    {
        $settings = array_merge($this->settings(), $payload);
        $this->write('settings.json', $settings);
    }

    public function categoryLabels(): array
    {
        return [
            'original' => 'Originals',
            'print' => 'Prints',
            'commission_example' => 'Commission examples',
        ];
    }

    public function statusLabels(): array
    {
        return [
            'for_sale' => 'For sale',
            'sold' => 'Sold',
            'reserved' => 'Reserved',
            'hidden' => 'Hidden',
        ];
    }

    protected function read(string $file): array
    {
        $path = $this->basePath . DIRECTORY_SEPARATOR . $file;
        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode(File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function write(string $file, array $data): void
    {
        File::put($this->basePath . DIRECTORY_SEPARATOR . $file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    protected function normalizeProduct(array $product): array
    {
        $images = array_values(array_filter(array_map(
            fn ($image) => trim((string) $image),
            is_array($product['images'] ?? null) ? $product['images'] : []
        )));

        if (count($images) === 0 && ! empty($product['image'])) {
            $images = [trim((string) $product['image'])];
        }

        $category = trim((string) ($product['category'] ?? 'original'));
        if (! array_key_exists($category, $this->categoryLabels())) {
            $category = 'original';
        }

        $status = trim((string) ($product['status'] ?? 'for_sale'));
        if (! array_key_exists($status, $this->statusLabels())) {
            $status = 'for_sale';
        }

        $name = trim((string) ($product['name'] ?? 'Untitled artwork'));

        return [
            'name' => $name,
            'slug' => trim((string) ($product['slug'] ?? Str::slug($name ?: 'untitled-artwork'))),
            'price' => trim((string) ($product['price'] ?? 'Price on request')),
            'tag' => trim((string) ($product['tag'] ?? 'Original artwork')),
            'description' => trim((string) ($product['description'] ?? '')),
            'size' => trim((string) ($product['size'] ?? 'Available on request')),
            'story' => trim((string) ($product['story'] ?? '')),
            'status' => $status,
            'category' => $category,
            'button_label' => trim((string) ($product['button_label'] ?? 'Ask to buy')),
            'images' => $images,
            'featured' => (bool) ($product['featured'] ?? true),
        ];
    }

    protected function normalizeMessage(array $message): array
    {
        return [
            'id' => trim((string) ($message['id'] ?? '')) ?: (string) Str::uuid(),
            'name' => trim((string) ($message['name'] ?? '')),
            'email' => trim((string) ($message['email'] ?? '')),
            'phone' => trim((string) ($message['phone'] ?? '')),
            'commission_type' => trim((string) ($message['commission_type'] ?? '')),
            'subject' => trim((string) ($message['subject'] ?? '')),
            'message' => trim((string) ($message['message'] ?? '')),
            'status' => in_array(($message['status'] ?? 'new'), ['new', 'in_progress', 'replied', 'archived'], true) ? $message['status'] : 'new',
            'created_at' => trim((string) ($message['created_at'] ?? now()->toDateTimeString())),
        ];
    }

    protected function normalizeOrder(array $order): array
    {
        return [
            'id' => trim((string) ($order['id'] ?? 'CSA-'.Str::upper(Str::random(8)))),
            'date' => trim((string) ($order['date'] ?? $order['created_at'] ?? now()->toDateTimeString())),
            'created_at' => trim((string) ($order['created_at'] ?? $order['date'] ?? now()->toDateTimeString())),
            'customer' => is_array($order['customer'] ?? null) ? $order['customer'] : [
                'first_name' => trim((string) ($order['name'] ?? '')),
                'last_name' => '',
                'email' => trim((string) ($order['email'] ?? '')),
                'phone' => trim((string) ($order['phone'] ?? '')),
                'address' => '',
                'city' => '',
                'postal_code' => '',
                'country' => '',
                'message' => trim((string) ($order['message'] ?? '')),
            ],
            'items' => is_array($order['items'] ?? null) ? array_values(array_map(fn ($item) => $this->normalizeOrderItem(is_array($item) ? $item : []), $order['items'])) : [[
                'name' => trim((string) ($order['product'] ?? 'Artwork inquiry')),
                'price' => trim((string) ($order['price'] ?? 'Price on request')),
                'type' => 'original',
                'source' => 'manual inquiry',
            ]],
            'subtotal' => (float) ($order['subtotal'] ?? 0),
            'payment_status' => trim((string) ($order['payment_status'] ?? 'pending')),
            'fulfillment_status' => trim((string) ($order['fulfillment_status'] ?? 'not_sent')),
            'stripe_session_id' => trim((string) ($order['stripe_session_id'] ?? '')),
            'printful_order_id' => trim((string) ($order['printful_order_id'] ?? '')),
            'admin_note' => trim((string) ($order['admin_note'] ?? '')),
        ];
    }

    protected function normalizeTestimonial(array $testimonial): array
    {
        return [
            'id' => trim((string) ($testimonial['id'] ?? '')) ?: (string) Str::uuid(),
            'name' => trim((string) ($testimonial['name'] ?? 'Happy collector')),
            'context' => trim((string) ($testimonial['context'] ?? 'Commission client')),
            'body' => trim((string) ($testimonial['body'] ?? '')),
            'image' => trim((string) ($testimonial['image'] ?? 'assets/images/claire-gallery/testimonial-boy-with-dog.jpg')),
            'featured' => (bool) ($testimonial['featured'] ?? false),
            'created_at' => trim((string) ($testimonial['created_at'] ?? now()->toDateTimeString())),
        ];
    }

    protected function normalizeSubscriber(array $subscriber): array
    {
        return [
            'id' => trim((string) ($subscriber['id'] ?? '')) ?: (string) Str::uuid(),
            'name' => trim((string) ($subscriber['name'] ?? '')),
            'email' => trim((string) ($subscriber['email'] ?? '')),
            'created_at' => trim((string) ($subscriber['created_at'] ?? now()->toDateTimeString())),
        ];
    }

    protected function normalizePrintfulProduct(array $product): array
    {
        $variants = is_array($product['variants'] ?? null) ? array_values($product['variants']) : [];
        $defaultVariantId = trim((string) ($product['default_variant_id'] ?? $product['sync_variant_id'] ?? ($variants[0]['id'] ?? $variants[0]['sync_variant_id'] ?? $product['id'] ?? '')));

        return [
            'id' => trim((string) ($product['id'] ?? $product['external_id'] ?? Str::uuid())),
            'name' => trim((string) ($product['name'] ?? 'Printful product')),
            'thumbnail_url' => trim((string) ($product['thumbnail_url'] ?? $product['thumbnail'] ?? '')),
            'description' => trim((string) ($product['description'] ?? '')),
            'price' => $this->cleanPrintfulPriceLabel(trim((string) ($product['price'] ?? 'Resync for exact price'))),
            'synced_at' => trim((string) ($product['synced_at'] ?? now()->toDateTimeString())),
            'status' => trim((string) ($product['status'] ?? 'synced')),
            'default_variant_id' => $defaultVariantId,
            'variants' => $variants,
        ];
    }

    protected function normalizeOrderItem(array $item): array
    {
        return [
            'id' => trim((string) ($item['id'] ?? $item['slug'] ?? '')),
            'slug' => trim((string) ($item['slug'] ?? $item['id'] ?? '')),
            'name' => trim((string) ($item['name'] ?? 'Item')),
            'description' => trim((string) ($item['description'] ?? '')),
            'display_price' => trim((string) ($item['display_price'] ?? $item['price'] ?? '')),
            'price' => trim((string) ($item['price'] ?? $item['display_price'] ?? '')),
            'image' => trim((string) ($item['image'] ?? '')),
            'type' => trim((string) ($item['type'] ?? 'original')),
            'source' => trim((string) ($item['source'] ?? 'local')),
            'sync_variant_id' => trim((string) ($item['sync_variant_id'] ?? $item['default_variant_id'] ?? $item['id'] ?? '')),
        ];
    }

    protected function cleanPrintfulPriceLabel(string $price): string
    {
        $price = trim($price);
        if ($price === '' || strcasecmp($price, 'Price from Printful') === 0) {
            return 'Resync for exact price';
        }

        return $price;
    }

    protected function seedDefaults(): void
    {
        foreach (['products.json', 'messages.json', 'orders.json', 'testimonials.json', 'subscribers.json', 'printful_products.json', 'settings.json'] as $file) {
            if (! File::exists($this->basePath . DIRECTORY_SEPARATOR . $file)) {
                $this->write($file, []);
            }
        }

        if ($this->read('products.json') === []) {
            $this->write('products.json', $this->defaultProducts());
        }

        if ($this->read('testimonials.json') === []) {
            $this->write('testimonials.json', $this->defaultTestimonials());
        }
    }

    protected function defaultProducts(): array
    {
        return [
            [
                'name' => 'Cafeteria Cafe Latte',
                'slug' => 'cafeteria-cafe-latte',
                'price' => '550 DH',
                'tag' => 'Original watercolor',
                'description' => 'A warm watercolor study inspired by a quiet coffee moment, soft architectural lines, and Claire’s colorful travel sketchbook feeling.',
                'size' => 'Original artwork · size on request',
                'story' => 'Painted as a small everyday scene, this piece carries the comfort of travel, coffee, and slow observation.',
                'category' => 'original',
                'status' => 'for_sale',
                'button_label' => 'Ask to buy',
                'images' => ['assets/images/claire-gallery/cafeteria-cafe-latte.jpg', 'assets/images/1.png', 'assets/images/6.png'],
                'featured' => true,
            ],
            [
                'name' => 'Snow at AUI',
                'slug' => 'snow-at-aui',
                'price' => '550 DH',
                'tag' => 'Original watercolor',
                'description' => 'A peaceful snowy campus scene with gentle color, quiet architecture, and a soft winter atmosphere.',
                'size' => 'Original artwork · size on request',
                'story' => 'A memory from AUI transformed into a delicate watercolor scene full of space, light, and calm.',
                'category' => 'original',
                'status' => 'for_sale',
                'button_label' => 'Ask to buy',
                'images' => ['assets/images/claire-gallery/snow-at-aui.jpg', 'assets/images/2.png', 'assets/images/9.png'],
                'featured' => true,
            ],
            [
                'name' => 'AUI Mosque',
                'slug' => 'aui-mosque',
                'price' => '550 DH',
                'tag' => 'Original watercolor',
                'description' => 'A colorful architectural watercolor inspired by the mosque at AUI, balancing travel memory and handmade detail.',
                'size' => 'Original artwork · size on request',
                'story' => 'This piece captures the rhythm of Moroccan architecture through Claire’s warm watercolor style.',
                'category' => 'original',
                'status' => 'for_sale',
                'button_label' => 'Ask to buy',
                'images' => ['assets/images/claire-gallery/aui-mosque.jpg', 'assets/images/3.png', 'assets/images/5.png'],
                'featured' => true,
            ],
            [
                'name' => 'Moroccan Door',
                'slug' => 'moroccan-door',
                'price' => 'Sold',
                'tag' => 'Collected original',
                'description' => 'A bright watercolor sketch inspired by a blue Moroccan door, warm brick textures, and everyday street beauty.',
                'size' => 'A5 sketchbook page',
                'story' => 'Claire turned the geometric ironwork, bright blue paint, and warm wall tones into a watercolor that feels both architectural and personal.',
                'category' => 'original',
                'status' => 'sold',
                'button_label' => 'Request similar',
                'images' => ['assets/images/moroccan-door.jpg', 'assets/images/4.png', 'assets/images/7.png'],
                'featured' => false,
            ],
            [
                'name' => 'AUI Campus',
                'slug' => 'aui-campus',
                'price' => 'Sold',
                'tag' => 'Collected original',
                'description' => 'A watercolor moment from campus life in Morocco, balancing open sky, warm roofs, and architectural rhythm.',
                'size' => 'A5 sketchbook page',
                'story' => 'Painted from Claire’s study-abroad surroundings, this artwork captures the quiet openness of campus life.',
                'category' => 'original',
                'status' => 'sold',
                'button_label' => 'Request similar',
                'images' => ['assets/images/aui-campus.jpg', 'assets/images/8.png', 'assets/images/10.png'],
                'featured' => false,
            ],
            [
                'name' => 'Dog Portrait Commission Example',
                'slug' => 'dog-portrait-commission-example',
                'price' => 'Custom quote',
                'tag' => 'Commission example',
                'description' => 'Custom pet portraits created from client photos with warm personality and handmade charm.',
                'size' => 'Custom size on request',
                'story' => 'A commission example showing how Claire turns favorite pet photos into meaningful artwork.',
                'category' => 'commission_example',
                'status' => 'for_sale',
                'button_label' => 'Order commission',
                'images' => ['assets/images/claire-gallery/dog-portrait-sienna-ridd.jpg', 'assets/images/claire-gallery/dog-portrait-casey.jpg', 'assets/images/claire-gallery/dog-portrait-roxy.jpg'],
                'featured' => true,
            ],
            [
                'name' => 'Baby Portrait Commission Example',
                'slug' => 'baby-portrait-commission-example',
                'price' => 'Custom quote',
                'tag' => 'Commission example',
                'description' => 'Personal baby portraits for families, gifts, and keepsake memories.',
                'size' => 'Custom size on request',
                'story' => 'Soft, colorful portrait commissions made from client photos and special family memories.',
                'category' => 'commission_example',
                'status' => 'for_sale',
                'button_label' => 'Order commission',
                'images' => ['assets/images/claire-gallery/baby-portrait-pink-bow.jpg', 'assets/images/claire-gallery/baby-portrait-red-shirt.jpg', 'assets/images/claire-gallery/baby-portrait-smiling.jpg'],
                'featured' => true,
            ],
            [
                'name' => 'Landscape Commission Example',
                'slug' => 'landscape-commission-example',
                'price' => 'Custom quote',
                'tag' => 'Commission example',
                'description' => 'Custom places, family scenes, travel memories, and landscape stories painted from client ideas.',
                'size' => 'Custom size on request',
                'story' => 'A custom scene can turn a memory, place, or photograph into a handmade artwork.',
                'category' => 'commission_example',
                'status' => 'for_sale',
                'button_label' => 'Order commission',
                'images' => ['assets/images/claire-gallery/landscape-boy-dog.jpg', 'assets/images/claire-gallery/testimonial-boy-with-dog.jpg'],
                'featured' => true,
            ],
        ];
    }

    protected function defaultTestimonials(): array
    {
        return [
            [
                'id' => 'father-day-commission',
                'name' => 'Commission client',
                'context' => 'Father’s Day gift',
                'body' => 'My dad LOVED the painting. He said to tell you that you did a great job. Thanks for being part of it!!',
                'image' => 'assets/images/claire-gallery/testimonial-boy-with-dog.jpg',
                'featured' => true,
                'created_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 'pet-portrait-client',
                'name' => 'Pet portrait client',
                'context' => 'Dog portrait commission',
                'body' => 'The portrait captured the dogs so sweetly and made such a personal gift.',
                'image' => 'assets/images/claire-gallery/dog-portrait-casey.jpg',
                'featured' => false,
                'created_at' => now()->toDateTimeString(),
            ],
        ];
    }
}
