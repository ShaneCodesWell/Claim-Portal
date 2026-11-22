<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Api\V1\Concerns\ResolvesOwnedResources;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PolicyResource;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    use ResolvesOwnedResources;

    public function index(Request $request)
    {
        $request->validate([
            'search'          => ['nullable', 'string', 'max:100'],
            'type'            => ['nullable', 'string', 'max:100'],
            'include_expired' => ['nullable', 'boolean'],
            'per_page'        => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $policies = Policy::whereIn('customer_id', $this->ownedIds($request))
            ->when(! $request->boolean('include_expired'), fn ($q) => $q->visible())
            ->search($request->input('search'))
            ->ofType($request->input('type'))
            ->orderByRaw('end_date DESC NULLS LAST')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return PolicyResource::collection($policies)->additional([
            // Policies load in the background after login. Retry until this is true.
            'sync' => ['synced' => $request->user()->last_synced_at !== null],
        ]);
    }

    public function show(Request $request, int $policy)
    {
        return new PolicyResource($this->policyFor($request, $policy));
    }
}