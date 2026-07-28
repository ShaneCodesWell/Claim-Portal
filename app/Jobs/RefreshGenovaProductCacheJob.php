<?php

namespace App\Jobs;

use App\Models\GenovaProduct;
use App\Models\GenovaBusinessClass;
use App\Services\GenovaApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RefreshGenovaProductCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function handle(GenovaApiService $genova): void
    {
        $referencePhone = config('services.genova.reference_phone');

        try {
            $classesResponse = $genova->getBusinessClasses($referencePhone);
        } catch (\Exception $e) {
            Log::error('RefreshGenovaProductCacheJob: business-class call failed', [
                'error' => $e->getMessage(),
            ]);
            return;
        }

        if ($classesResponse->failed()) {
            Log::error('RefreshGenovaProductCacheJob: business-class non-200', [
                'status' => $classesResponse->status(),
            ]);
            return;
        }

        $classes = $classesResponse->json('data.content') ?? [];

        foreach ($classes as $id => $class) {
            GenovaBusinessClass::updateOrCreate(
                ['esu_main_product_id' => $id],
                ['name' => $class['name'] ?? 'Unknown Class']
            );

            try {
                $productsResponse = $genova->getProductsByClass($id);
            } catch (\Exception $e) {
                Log::warning('RefreshGenovaProductCacheJob: products-by-class failed', [
                    'business_class_id' => $id,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            if ($productsResponse->failed()) {
                Log::warning('RefreshGenovaProductCacheJob: products-by-class non-200', [
                    'business_class_id' => $id,
                    'status' => $productsResponse->status(),
                ]);
                continue;
            }

            $products = $productsResponse->json('data.content') ?? [];

            foreach ($products as $productId => $product) {
                GenovaProduct::updateOrCreate(
                    ['esu_product_id' => $productId],
                    [
                        'esu_main_product_id' => $id,
                        'name' => $product['name'] ?? 'Unknown Product',
                    ]
                );
            }
        }

        Log::info('RefreshGenovaProductCacheJob: completed', [
            'classes_synced' => count($classes),
        ]);
    }
}
