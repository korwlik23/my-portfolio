<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class HealthCheckService
{
    public function check(): array
    {
        $checks = [
            'database' => $this->database(),
            'cache' => $this->cache(),
            'storage' => $this->storage(),
            'queue' => $this->queue(),
        ];

        $healthy = collect($checks)->every(fn ($check) => $check['ok'] === true);

        return [
            'status' => $healthy ? 'ok' : 'degraded',
            'checked_at' => now()->toISOString(),
            'checks' => $checks,
        ];
    }

    private function database(): array
    {
        try {
            DB::select('select 1');
            return $this->ok();
        } catch (\Throwable) {
            return $this->fail();
        }
    }

    private function cache(): array
    {
        try {
            $key = 'health:' . str()->uuid();
            Cache::put($key, 'ok', 30);

            return Cache::get($key) === 'ok' ? $this->ok() : $this->fail();
        } catch (\Throwable) {
            return $this->fail();
        }
    }

    private function storage(): array
    {
        try {
            $path = 'health/.check';
            Storage::disk('local')->put($path, now()->toISOString());
            Storage::disk('local')->delete($path);

            return $this->ok();
        } catch (\Throwable) {
            return $this->fail();
        }
    }

    private function queue(): array
    {
        if (!config('monitoring.check_queue_table', true)) {
            return ['ok' => true, 'status' => 'skipped'];
        }

        try {
            return Schema::hasTable(config('queue.connections.database.table', 'jobs'))
                ? $this->ok()
                : $this->fail();
        } catch (\Throwable) {
            return $this->fail();
        }
    }

    private function ok(): array
    {
        return ['ok' => true, 'status' => 'ok'];
    }

    private function fail(): array
    {
        return ['ok' => false, 'status' => 'failed'];
    }
}
