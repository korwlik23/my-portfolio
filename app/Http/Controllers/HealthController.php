<?php

namespace App\Http\Controllers;

use App\Services\HealthCheckService;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function __invoke(Request $request, HealthCheckService $health)
    {
        $token = config('monitoring.health_token');

        if ($token && !hash_equals((string) $token, (string) $request->query('token'))) {
            return response()->json(['status' => 'forbidden'], 403);
        }

        $result = $health->check();

        return response()->json($result, $result['status'] === 'ok' ? 200 : 503);
    }
}
