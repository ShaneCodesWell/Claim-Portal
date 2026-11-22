<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait LoadsAuthorisedPolicy
{
    protected function authorisedPolicy(Request $request): Policy
    {
        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);

        $id = (string) $request->query('policyId');

        return Policy::whereIn('customer_id', $customer->resolvedCustomerIds())
            ->where(function ($q) use ($id) {
                $q->where('external_policy_id', $id);

                if (ctype_digit($id)) {
                    $q->orWhere('id', (int) $id);
                }
            })
            ->firstOrFail();
    }
}
