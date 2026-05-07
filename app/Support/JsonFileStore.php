<?php

namespace App\Support;

class JsonFileStore
{
    public static function read(string $path, array $fallback = []): array
    {
        self::ensureDirectory(dirname($path));

        if (! file_exists($path) || trim((string) file_get_contents($path)) === '') {
            self::write($path, $fallback);

            return $fallback;
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : $fallback;
    }

    public static function write(string $path, array $payload): void
    {
        self::ensureDirectory(dirname($path));

        file_put_contents(
            $path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    protected static function ensureDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}
