<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\RefreshGenovaProductCacheJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Refresh the Genova product/class cache nightly, then backfill any
// policies that were synced with Unknown Class/Product before the
// cache had their IDs.
Schedule::job(new RefreshGenovaProductCacheJob)->daily()->at('00:00');
Schedule::command('genova:backfill-product-names')->daily()->at('00:15');

// Sync every agent's GLIMS + Genova policies (and customer detail) nightly,
// so data doesn't go stale between agent logins. Runs after the Genova
// product cache is refreshed above, since Genova policy sync resolves
// product/business-class names from that cache.
// withoutOverlapping() guards against a slow run (e.g. many agents, or
// GLIMS being slow) still executing when the next night's run fires.
Schedule::command('agents:sync-policies')
    ->daily()
    ->at('01:00')
    ->withoutOverlapping();
