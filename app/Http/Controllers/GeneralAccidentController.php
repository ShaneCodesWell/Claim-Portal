<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGeneralAccidentRequest;
use App\Http\Requests\UpdateGeneralAccidentRequest;
use App\Models\GeneralAccident;
use Illuminate\Http\Request;
use App\Models\Policy;
use Illuminate\Support\Facades\Auth;

class GeneralAccidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $policyId = $request->query('policyId');
        $policy = Policy::where('external_policy_id', $policyId)->firstOrFail();
        $customer = $policy->customer;
        return view('general_accident_form.index', compact('policy', 'customer'));
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
