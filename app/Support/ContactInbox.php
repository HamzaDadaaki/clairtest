<?php

namespace App\Support;

use Illuminate\Support\Str;

class ContactInbox
{
    public static function all(): array
    {
        $messages = JsonFileStore::read(self::storagePath(), []);

        return collect($messages)
            ->filter(fn ($item) => is_array($item))
            ->map(fn (array $item) => self::normalize($item))
            ->sortByDesc('created_at')
            ->values()
            ->all();
    }

    public static function stats(): array
    {
        $messages = self::all();

        return [
            'total' => count($messages),
            'new' => count(array_filter($messages, fn (array $message) => $message['status'] === 'new')),
            'in_progress' => count(array_filter($messages, fn (array $message) => $message['status'] === 'in_progress')),
            'replied' => count(array_filter($messages, fn (array $message) => $message['status'] === 'replied')),
        ];
    }

    public static function find(string $id): ?array
    {
        foreach (self::all() as $message) {
            if ($message['id'] === $id) {
                return $message;
            }
        }

        return null;
    }

    public static function create(array $message): array
    {
        $messages = self::all();
        $normalized = self::normalize($message);
        $messages[] = $normalized;
        JsonFileStore::write(self::storagePath(), array_values($messages));

        return $normalized;
    }

    public static function update(string $id, array $changes): ?array
    {
        $messages = self::all();
        $updatedRecord = null;

        foreach ($messages as $index => $message) {
            if ($message['id'] !== $id) {
                continue;
            }

            $messages[$index] = self::normalize(array_merge($message, $changes, ['id' => $id]));
            $updatedRecord = $messages[$index];
            break;
        }

        if ($updatedRecord !== null) {
            JsonFileStore::write(self::storagePath(), array_values($messages));
        }

        return $updatedRecord;
    }

    public static function delete(string $id): void
    {
        JsonFileStore::write(
            self::storagePath(),
            array_values(array_filter(self::all(), fn (array $message) => $message['id'] !== $id))
        );
    }

    protected static function normalize(array $message): array
    {
        $allowedStatuses = ['new', 'in_progress', 'replied', 'archived'];
        $status = (string) ($message['status'] ?? 'new');

        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'new';
        }

        return [
            'id' => trim((string) ($message['id'] ?? '')) ?: (string) Str::uuid(),
            'name' => trim((string) ($message['name'] ?? '')),
            'email' => trim((string) ($message['email'] ?? '')),
            'phone' => trim((string) ($message['phone'] ?? '')),
            'subject' => trim((string) ($message['subject'] ?? '')),
            'message' => trim((string) ($message['message'] ?? '')),
            'status' => $status,
            'admin_note' => trim((string) ($message['admin_note'] ?? '')),
            'ip' => trim((string) ($message['ip'] ?? '')),
            'source' => trim((string) ($message['source'] ?? 'website form')),
            'created_at' => trim((string) ($message['created_at'] ?? now()->toDateTimeString())),
        ];
    }

    protected static function storagePath(): string
    {
        return storage_path('app/contact_messages.json');
    }
}
