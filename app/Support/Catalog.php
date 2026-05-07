<?php

namespace App\Support;

use Illuminate\Support\Str;

class Catalog
{
    public static function all(): array
    {
        self::ensureStorageFile();

        $decoded = json_decode((string) file_get_contents(self::storagePath()), true);

        if (! is_array($decoded)) {
            $decoded = self::seedCatalog();
            self::saveAll($decoded);
        }

        return array_values(array_map(fn (array $item) => self::normalizeProduct($item), $decoded));
    }

    public static function saveAll(array $products): void
    {
        $directory = dirname(self::storagePath());

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $normalized = array_values(array_map(fn (array $item) => self::normalizeProduct($item), $products));

        file_put_contents(
            self::storagePath(),
            json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $item) {
            if ($item['slug'] === $slug) {
                return $item;
            }
        }

        return null;
    }

    public static function featured(): array
    {
        return array_values(array_filter(self::all(), fn ($item) => $item['featured'] === true));
    }

    public static function latest(): array
    {
        return array_values(array_filter(self::all(), fn ($item) => $item['latest'] === true));
    }

    public static function related(array $product): array
    {
        $related = $product['related'] ?? [];

        return array_values(array_filter(self::all(), function ($item) use ($related, $product) {
            return $item['slug'] !== ($product['slug'] ?? null) && in_array($item['slug'], $related, true);
        }));
    }

    public static function stats(): array
    {
        return [
            ['value' => '180+', 'label' => 'Businesses served'],
            ['value' => '320+', 'label' => 'Projects and deployments'],
            ['value' => '24/7', 'label' => 'Commercial reach'],
            ['value' => '99%', 'label' => 'Client satisfaction mindset'],
        ];
    }

    public static function search(?string $query): array
    {
        $query = trim((string) $query);

        if ($query === '') {
            return self::all();
        }

        $query = mb_strtolower($query);

        return array_values(array_filter(self::all(), function ($item) use ($query) {
            $reviews = array_map(fn ($review) => implode(' ', [
                $review['name'] ?? '',
                $review['role'] ?? '',
                $review['text'] ?? '',
            ]), $item['review'] ?? []);

            $haystack = mb_strtolower(implode(' ', [
                $item['name'] ?? '',
                $item['type'] ?? '',
                $item['badge'] ?? '',
                $item['price_label'] ?? '',
                $item['short'] ?? '',
                $item['description'] ?? '',
                $item['hero_note'] ?? '',
                implode(' ', $item['features'] ?? []),
                implode(' ', $item['related'] ?? []),
                implode(' ', $reviews),
            ]));

            return str_contains($haystack, $query);
        }));
    }

    public static function slugExists(string $slug, ?string $ignoreSlug = null): bool
    {
        foreach (self::all() as $item) {
            if ($item['slug'] === $slug && $item['slug'] !== $ignoreSlug) {
                return true;
            }
        }

        return false;
    }

    public static function upsert(array $product, ?string $originalSlug = null): array
    {
        $products = self::all();
        $normalized = self::normalizeProduct($product);
        $updated = false;

        foreach ($products as $index => $item) {
            if ($item['slug'] === $originalSlug) {
                $products[$index] = $normalized;
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            $products[] = $normalized;
        }

        if ($originalSlug !== null && $originalSlug !== $normalized['slug']) {
            foreach ($products as $index => $item) {
                $related = array_map(function ($relatedSlug) use ($originalSlug, $normalized) {
                    return $relatedSlug === $originalSlug ? $normalized['slug'] : $relatedSlug;
                }, $item['related'] ?? []);

                $products[$index]['related'] = array_values(array_unique($related));
            }
        }

        self::saveAll($products);

        return $normalized;
    }

    public static function delete(string $slug): void
    {
        $products = array_values(array_filter(self::all(), fn ($item) => $item['slug'] !== $slug));

        foreach ($products as $index => $item) {
            $products[$index]['related'] = array_values(array_filter($item['related'] ?? [], fn ($relatedSlug) => $relatedSlug !== $slug));
        }

        self::saveAll($products);
    }

    protected static function ensureStorageFile(): void
    {
        $path = self::storagePath();
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (! file_exists($path) || trim((string) file_get_contents($path)) === '') {
            self::saveAll(self::seedCatalog());
        }
    }

    protected static function storagePath(): string
    {
        return storage_path('app/products.json');
    }

    protected static function normalizeProduct(array $item): array
    {
        $slug = trim((string) ($item['slug'] ?? ''));
        $slug = $slug !== '' ? Str::slug($slug) : Str::slug((string) ($item['name'] ?? ''));
        $slug = $slug !== '' ? $slug : 'product-'.Str::lower(Str::random(8));

        $features = array_values(array_filter(array_map(function ($feature) {
            return trim((string) $feature);
        }, is_array($item['features'] ?? null) ? $item['features'] : [])));

        $related = array_values(array_filter(array_map(function ($relatedItem) {
            return Str::slug(trim((string) $relatedItem));
        }, is_array($item['related'] ?? null) ? $item['related'] : [])));

        $gallery = array_values(array_filter(array_map(function ($image) {
            return trim((string) $image);
        }, is_array($item['gallery'] ?? null) ? $item['gallery'] : [])));

        $primaryImage = trim((string) ($item['image'] ?? ''));

        if ($primaryImage === '' && isset($gallery[0])) {
            $primaryImage = $gallery[0];
        }

        if ($primaryImage !== '' && ! in_array($primaryImage, $gallery, true)) {
            array_unshift($gallery, $primaryImage);
        }

        $gallery = array_values(array_unique($gallery));

        $reviews = [];

        if (is_array($item['review'] ?? null)) {
            foreach ($item['review'] as $review) {
                if (! is_array($review)) {
                    continue;
                }

                $name = trim((string) ($review['name'] ?? ''));
                $role = trim((string) ($review['role'] ?? ''));
                $text = trim((string) ($review['text'] ?? ''));

                if ($name === '' && $role === '' && $text === '') {
                    continue;
                }

                $reviews[] = [
                    'name' => $name,
                    'role' => $role,
                    'text' => $text,
                ];
            }
        }

        return [
            'slug' => $slug,
            'name' => trim((string) ($item['name'] ?? 'Untitled product')),
            'type' => in_array(($item['type'] ?? ''), ['Software', 'Service'], true) ? $item['type'] : 'Software',
            'badge' => trim((string) ($item['badge'] ?? '')),
            'price_label' => trim((string) ($item['price_label'] ?? 'Custom quote')),
            'short' => trim((string) ($item['short'] ?? '')),
            'description' => trim((string) ($item['description'] ?? '')),
            'features' => $features,
            'review' => $reviews,
            'hero_note' => trim((string) ($item['hero_note'] ?? '')),
            'related' => $related,
            'featured' => filter_var($item['featured'] ?? false, FILTER_VALIDATE_BOOL),
            'latest' => filter_var($item['latest'] ?? false, FILTER_VALIDATE_BOOL),
            'gallery' => $gallery,
            'image' => $primaryImage !== '' ? $primaryImage : 'assets/images/products/afayar-pos.webp',
        ];
    }

    protected static function seedCatalog(): array
    {
        return [
            [
                'slug' => 'afayar-pos',
                'name' => 'Afayar POS',
                'type' => 'Software',
                'badge' => 'Best seller',
                'price_label' => 'From 1500 MAD / year',
                'short' => 'A smart point-of-sale platform for restaurants, boutiques, salons, and multi-branch businesses.',
                'description' => 'Afayar POS helps business owners sell faster, manage stock clearly, follow orders, and keep operations simple. It is designed for real-world shops and service businesses that need speed, clarity, and reliable daily workflow.',
                'features' => [
                    'Fast cashier experience',
                    'Product, category, and inventory management',
                    'Business branding and receipt settings',
                    'Reports for sales, products, and performance',
                    'Flexible setup for restaurant, salon, and boutique use',
                ],
                'review' => [
                    ['name' => 'Yassine B.', 'role' => 'Store Owner', 'text' => 'Afayar POS gave us a more professional checkout experience and saved a lot of time every day.'],
                    ['name' => 'Imane T.', 'role' => 'Salon Manager', 'text' => 'Simple to use, clean for staff, and powerful enough for our daily work.'],
                ],
                'hero_note' => 'Built to help Moroccan businesses sell with confidence.',
                'related' => ['afayar-inventory-cloud', 'pos-deployment-training', 'maintenance-support'],
                'featured' => true,
                'latest' => true,
                'gallery' => [
                    'assets/images/products/afayar-pos.webp',
                    'assets/images/products/afayar-pos-detail.webp',
                    'assets/images/products/afayar-pos-presentation.webp',
                ],
                'image' => 'assets/images/products/afayar-pos.webp',
            ],
            [
                'slug' => 'afayar-inventory-cloud',
                'name' => 'Afayar Inventory Cloud',
                'type' => 'Software',
                'badge' => 'Cloud ready',
                'price_label' => 'Custom quote',
                'short' => 'Track stock movement, low-stock alerts, and branch visibility from one dashboard.',
                'description' => 'Afayar Inventory Cloud is designed for companies that want clearer control of products, movement, restocking, and branch-level visibility. It reduces waste and improves planning.',
                'features' => [
                    'Low stock alerts',
                    'Movement history',
                    'Branch-level visibility',
                    'Simple dashboard for managers',
                    'Future-ready for advanced analytics',
                ],
                'review' => [
                    ['name' => 'Karim L.', 'role' => 'Retail Manager', 'text' => 'It made stock follow-up easier and more structured for our team.'],
                ],
                'hero_note' => 'See your stock before stock problems see you.',
                'related' => ['afayar-pos', 'custom-web-development', 'maintenance-support'],
                'featured' => true,
                'latest' => true,
                'gallery' => [
                    'assets/images/products/afayar-inventory-cloud.webp',
                    'assets/images/products/afayar-inventory-cloud-detail.webp',
                    'assets/images/products/afayar-inventory-cloud-presentation.webp',
                ],
                'image' => 'assets/images/products/afayar-inventory-cloud.webp',
            ],
            [
                'slug' => 'afayar-booking-suite',
                'name' => 'Afayar Booking Suite',
                'type' => 'Software',
                'badge' => 'For services',
                'price_label' => 'Custom quote',
                'short' => 'Appointment and booking software for salons, beauty spaces, and service businesses.',
                'description' => 'Afayar Booking Suite helps service businesses manage bookings, availability, customer follow-up, and a more premium customer experience.',
                'features' => [
                    'Appointment calendar',
                    'Service and staff scheduling',
                    'Customer reminders',
                    'Clean admin interface',
                    'Ready for growth',
                ],
                'review' => [
                    ['name' => 'Nadia S.', 'role' => 'Beauty Studio Owner', 'text' => 'Bookings became more organized and our clients felt the difference.'],
                ],
                'hero_note' => 'Turn appointments into a smoother business system.',
                'related' => ['afayar-pos', 'pos-deployment-training', 'custom-web-development'],
                'featured' => false,
                'latest' => true,
                'gallery' => [
                    'assets/images/products/afayar-booking-suite.webp',
                    'assets/images/products/afayar-booking-suite-detail.webp',
                    'assets/images/products/afayar-booking-suite-presentation.webp',
                ],
                'image' => 'assets/images/products/afayar-booking-suite.webp',
            ],
            [
                'slug' => 'custom-web-development',
                'name' => 'Custom Web Development',
                'type' => 'Service',
                'badge' => 'Tailored build',
                'price_label' => 'Custom quote',
                'short' => 'Modern websites and business platforms built around your real goals.',
                'description' => 'We design and develop professional websites and internal tools with a strong focus on clarity, design quality, speed, and business impact.',
                'features' => [
                    'Business showcase websites',
                    'Custom admin dashboards',
                    'Landing pages that convert',
                    'Premium visual direction',
                    'Built for scalability',
                ],
                'review' => [
                    ['name' => 'Salma A.', 'role' => 'Brand Founder', 'text' => 'The final website looked premium and matched our brand perfectly.'],
                ],
                'hero_note' => 'Your company deserves more than a basic website.',
                'related' => ['afayar-pos', 'afayar-booking-suite', 'maintenance-support'],
                'featured' => true,
                'latest' => false,
                'gallery' => [
                    'assets/images/products/custom-web-development.webp',
                    'assets/images/products/custom-web-development-detail.webp',
                    'assets/images/products/custom-web-development-presentation.webp',
                ],
                'image' => 'assets/images/products/custom-web-development.webp',
            ],
            [
                'slug' => 'pos-deployment-training',
                'name' => 'POS Deployment & Training',
                'type' => 'Service',
                'badge' => 'Hands-on help',
                'price_label' => 'Included or custom',
                'short' => 'We help install, configure, and train your team for a smooth launch.',
                'description' => 'A good product is not enough without a smooth start. Afayar supports businesses with deployment, first setup, and practical team training.',
                'features' => [
                    'Initial setup assistance',
                    'Staff onboarding',
                    'Configuration guidance',
                    'Launch support',
                    'Better first-week confidence',
                ],
                'review' => [
                    ['name' => 'Rachid M.', 'role' => 'Restaurant Owner', 'text' => 'The training helped my staff adapt quickly and avoid confusion.'],
                ],
                'hero_note' => 'Launch faster with expert guidance.',
                'related' => ['afayar-pos', 'maintenance-support', 'afayar-inventory-cloud'],
                'featured' => false,
                'latest' => false,
                'gallery' => [
                    'assets/images/products/pos-deployment-training.webp',
                    'assets/images/products/pos-deployment-training-detail.webp',
                    'assets/images/products/pos-deployment-training-presentation.webp',
                ],
                'image' => 'assets/images/products/pos-deployment-training.webp',
            ],
            [
                'slug' => 'maintenance-support',
                'name' => 'Maintenance & Support',
                'type' => 'Service',
                'badge' => 'Reliable support',
                'price_label' => 'Monthly or yearly',
                'short' => 'Stay secure, updated, and supported with technical maintenance from Afayar.',
                'description' => 'We keep client systems stable, updated, and professionally supported so businesses can focus on selling, serving, and growing.',
                'features' => [
                    'Corrective updates',
                    'Design and UX improvements',
                    'Bug fixes and monitoring',
                    'Priority support options',
                    'Long-term partnership',
                ],
                'review' => [
                    ['name' => 'Othmane D.', 'role' => 'Operations Lead', 'text' => 'Fast support and clear communication made a big difference for us.'],
                ],
                'hero_note' => 'A serious business needs serious technical follow-up.',
                'related' => ['afayar-pos', 'afayar-inventory-cloud', 'custom-web-development'],
                'featured' => true,
                'latest' => true,
                'gallery' => [
                    'assets/images/products/maintenance-support.webp',
                    'assets/images/products/maintenance-support-detail.webp',
                    'assets/images/products/maintenance-support-presentation.webp',
                ],
                'image' => 'assets/images/products/maintenance-support.webp',
            ],
        ];
    }
}
