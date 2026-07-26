<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortfolioMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isMaintenanceEnabled()) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        return response()
            ->view('portfolio.maintenance', status: 503)
            ->header('Retry-After', '3600');
    }

    private function isMaintenanceEnabled(): bool
    {
        return SiteSetting::value('maintenance_mode', '0') === '1';
    }

    private function shouldBypass(Request $request): bool
    {
        if ($request->user()?->can('admin.access')) {
            return true;
        }

        return $request->is(
            'admin',
            'admin/*',
            'dashboard',
            'settings',
            'settings/*',
            'profile',
            'login',
            'logout',
            'forgot-password',
            'reset-password/*',
            'email/*',
            'health',
            'up',
            'lang/*',
            'resume/*',
            'robots.txt',
            'sitemap.xml',
            'build/*',
            'storage/*',
            'media/*',
        );
    }
}
