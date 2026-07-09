<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGeneralAccidentRequest;
use App\Http\Requests\UpdateGeneralAccidentRequest;
use App\Models\Customer;
use App\Models\GeneralAccident;
use App\Models\Policy;
use Illuminate\Http\Request;
use App\Traits\MergesClaimDraftData;

class GeneralAccidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use MergesClaimDraftData;

    public function index(Request $request)
    {
        $policyId = $request->query('policyId');
        $policy   = Policy::where('external_policy_id', $policyId)->orWhere('id', $policyId)->firstOrFail();
        $customer = Customer::findOrFail($policy->customer_id);

        $formData = [
            'fullname' => $customer->name ?? '',
            'email'    => $customer->email ?? '',
            'phone'    => $customer->phone ?? '',
        ];

        $draft    = $this->findDraftFor($customer, $policy, 'general_accident');
        $formData = $draft ? array_merge($formData, $draft->form_data ?? []) : $formData;

        return view('forms.general_accident_form.index', compact('policy', 'customer', 'policyId', 'formData', 'draft'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGeneralAccidentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneralAccident $generalAccident)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeneralAccident $generalAccident)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGeneralAccidentRequest $request, GeneralAccident $generalAccident)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneralAccident $generalAccident)
    {
        //
    }
}
