<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use App\Http\Requests\StoreFireRequest;
use App\Http\Requests\UpdateFireRequest;
use App\Models\Fire;

class FireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $policyId = $request->query('policyId');
        // $policy = Policy::where('external_policy_id', $policyId)->firstOrFail(); // For now, we will just use the policy ID to find the policy. In the future, we can use the external policy ID to find the policy and then pass the policy ID to the form.
        $policy   = $policyId ? Policy::find($policyId) : null;
        $customer = $policy ? $policy->customer : null;
        return view('forms.fire_form.index', compact('policy', 'policyId', 'customer'));
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
    public function store(StoreFireRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Fire $fire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fire $fire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFireRequest $request, Fire $fire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fire $fire)
    {
        //
    }
}
