<?php

namespace App\Support;

use Illuminate\Support\Str;

class PartnerStore
{
    public static function all(): array
    {
        $partners = JsonFileStore::read(self::storagePath(), self::seed());

        return collect($partners)
            ->filter(fn ($item) => is_array($item))
            ->map(fn (array $item) => self::normalize($item))
            ->sortBy([
                ['sort_order', 'asc'],
                ['name', 'asc'],
            ])
            ->values()
            ->all();
    }

    public static function active(): array
    {
        return array_values(array_filter(self::all(), fn (array $item) => $item['is_active']));
    }

    public static function find(string $id): ?array
    {
        foreach (self::all() as $partner) {
            if ($partner['id'] === $id) {
                return $partner;
            }
        }

        return null;
    }

    public static function upsert(array $partner, ?string $originalId = null): array
    {
        $normalized = self::normalize($partner);
        $partners = self::all();
        $updated = false;

        foreach ($partners as $index => $existing) {
            if ($existing['id'] === $originalId) {
                $partners[$index] = $normalized;
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            $partners[] = $normalized;
        }

        JsonFileStore::write(self::storagePath(), array_values($partners));

        return $normalized;
    }

    public static function delete(string $id): void
    {
        JsonFileStore::write(
            self::storagePath(),
            array_values(array_filter(self::all(), fn (array $item) => $item['id'] !== $id))
        );
    }

    protected static function normalize(array $partner): array
    {
        $name = trim((string) ($partner['name'] ?? 'Partner'));
        $id = trim((string) ($partner['id'] ?? ''));

        return [
            'id' => $id !== '' ? $id : (string) Str::uuid(),
            'name' => $name,
            'website' => trim((string) ($partner['website'] ?? '')),
            'logo' => trim((string) ($partner['logo'] ?? 'assets/images/logo.png')),
            'tagline' => trim((string) ($partner['tagline'] ?? '')),
            'sort_order' => (int) ($partner['sort_order'] ?? 0),
            'is_active' => filter_var($partner['is_active'] ?? true, FILTER_VALIDATE_BOOL),
        ];
    }

    protected static function storagePath(): string
    {
        return storage_path('app/partners.json');
    }

    protected static function seed(): array
    {
        return [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Afayar',
                'website' => '',
                'logo' => 'assets/images/logo.png',
                'tagline' => 'Software development',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Amelle',
                'website' => '',
                'logo' => 'assets/images/amelle-logo.png',
                'tagline' => 'Creative brand partner',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];
    }
}
