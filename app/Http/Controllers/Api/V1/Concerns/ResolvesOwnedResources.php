<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\Policy;
use Illuminate\Http\Request;

trait ResolvesOwnedResources
{
    private ?array $ownedIds = null;

    protected function ownedIds(Request $request): array
    {
        return $this->ownedIds ??= $request->user()->resolvedCustomerIds();
    }

    // findOrFail -> 404 for "not yours" as well as "doesn't exist"
    protected function claimFor(Request $request, int $id): Claim
    {
        return Claim::whereIn('customer_id', $this->ownedIds($request))->findOrFail($id);
    }

    protected function policyFor(Request $request, int $id): Policy
    {
        return Policy::whereIn('customer_id', $this->ownedIds($request))->findOrFail($id);
    }

    protected function documentFor(Request $request, int $id): ClaimDocument
    {
        return ClaimDocument::whereHas('claim', fn($q) => $q->whereIn('customer_id', $this->ownedIds($request)))
            ->findOrFail($id);
    }
}
