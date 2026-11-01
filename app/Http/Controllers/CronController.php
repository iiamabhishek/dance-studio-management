<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

/**
 * Vercel Cron endpoint (serverless has no daemons).
 *
 * Runs due scheduler tasks (subscription expiry / invoice overdue)
 * then drains pending queued jobs. Guarded by CRON_SECRET.
 */
class CronController extends Controller
{
    public function run(): JsonResponse
    {
        $secret = config('cron.secret', env('CRON_SECRET'));

        if (empty($secret) || request()->header('X-Cron-Secret') !== $secret) {
            abort(404);
        }

        Artisan::call('schedule:run');
        Artisan::call('queue:work', ['--stop-when-empty' => true, '--max-jobs' => 100]);

        return response()->json(['status' => 'ok', 'output' => Artisan::output()]);
    }
}
