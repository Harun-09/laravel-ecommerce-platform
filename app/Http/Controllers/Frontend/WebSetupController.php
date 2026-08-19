<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class WebSetupController extends Controller
{
    public function run(Request $request, string $token): JsonResponse
    {
        if (!((bool) env('WEB_SETUP_ENABLED', false))) {
            abort(404);
        }

        $expectedToken = trim((string) env('WEB_SETUP_TOKEN', ''));
        if ($expectedToken === '' || !hash_equals($expectedToken, trim($token))) {
            abort(404);
        }

        if (trim((string) config('app.key', '')) === '') {
            return response()->json([
                'success' => false,
                'message' => 'APP_KEY is missing. Set APP_KEY in .env first, then run setup again.',
            ], 422);
        }

        $results = [];

        Artisan::call('migrate', ['--force' => true]);
        $results['migrate'] = trim((string) Artisan::output());

        if (!((bool) env('PUBLIC_DISK_USE_PUBLIC_PATH', false))) {
            try {
                Artisan::call('storage:link');
                $results['storage_link'] = trim((string) Artisan::output());
            } catch (\Throwable $exception) {
                $results['storage_link'] = 'Skipped: ' . $exception->getMessage();
            }
        } else {
            $results['storage_link'] = 'Skipped: PUBLIC_DISK_USE_PUBLIC_PATH=true';
        }

        Artisan::call('optimize:clear');
        $results['optimize_clear'] = trim((string) Artisan::output());

        Artisan::call('optimize');
        $results['optimize'] = trim((string) Artisan::output());

        return response()->json([
            'success' => true,
            'message' => 'Web setup completed.',
            'results' => $results,
        ]);
    }
}

