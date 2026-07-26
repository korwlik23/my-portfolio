<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_basic_status(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.database.status', 'ok')
            ->assertJsonPath('checks.cache.status', 'ok')
            ->assertJsonPath('checks.storage.status', 'ok')
            ->assertJsonPath('checks.queue.status', 'ok');
    }

    public function test_health_endpoint_can_require_a_token(): void
    {
        config(['monitoring.health_token' => 'secret-token']);

        $this->getJson('/health')->assertForbidden();
        $this->getJson('/health?token=secret-token')->assertOk();
    }
}
