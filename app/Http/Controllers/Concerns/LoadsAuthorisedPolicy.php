<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait LoadsAuthorisedPolicy
{
    protected function authorisedPolicy(Request $request): Policy
    {
        $id = (string) $request->query('policyId');

        $query = Policy::where(function ($q) use ($id) {
            $q->where('external_policy_id', $id);

            if (ctype_digit($id)) {
                $q->orWhere('id', (int) $id);
            }
        });

        if ($customer = Auth::guard('customer')->user()) {
            $query->whereIn('customer_id', $customer->resolvedCustomerIds());
        } elseif ($agent = Auth::guard('agent')->user()) {
            $query->where('agent_id', $agent->id);
        } else {
            abort(403);
        }

        return $query->firstOrFail();   // 404 when it isn't theirs
    }
}