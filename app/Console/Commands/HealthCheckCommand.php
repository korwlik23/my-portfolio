<?php

namespace App\Console\Commands;

use App\Services\HealthCheckService;
use App\Services\OperationalAlertService;
use Illuminate\Console\Command;

class HealthCheckCommand extends Command
{
    protected $signature = 'app:health-check {--alert : Send an operational alert when health is degraded}';

    protected $description = 'Run production health checks for monitoring and deploy verification';

    public function handle(HealthCheckService $health, OperationalAlertService $alerts): int
    {
        $result = $health->check();

        foreach ($result['checks'] as $name => $check) {
            $this->line(sprintf('%s: %s', $name, $check['status']));
        }

        $this->line('status: ' . $result['status']);

        if ($result['status'] !== 'ok' && $this->option('alert')) {
            $alerts->alert('Application health degraded', 'One or more health checks failed.', [
                'status' => $result['status'],
                'checked_at' => $result['checked_at'],
            ]);
        }

        return $result['status'] === 'ok' ? self::SUCCESS : self::FAILURE;
    }
}
