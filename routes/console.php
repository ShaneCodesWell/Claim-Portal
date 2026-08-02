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
