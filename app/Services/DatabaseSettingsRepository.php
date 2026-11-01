<?php

namespace App\Services;

use App\Contracts\SettingsRepository;
use Illuminate\Support\Facades\DB;

/**
 * Database-backed settings store.
 *
 * Production/serverless replacement for JsonSettingsRepository:
 * storage/data/*.json is ephemeral on Vercel, so the studio info is
 * persisted in a single-row `settings` table instead.
 */
class DatabaseSettingsRepository implements SettingsRepository
{
    private const KEY = 'gymie';

    public function get(): array
    {
        $row = DB::table('settings')->where('key', self::KEY)->value('payload');

        return $row ? (array) json_decode($row, true) : [];
    }

    public function put(array $settings): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => self::KEY],
            [
                'payload' => json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ],
        );
    }
}
