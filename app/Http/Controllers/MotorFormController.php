<?php

namespace App\Http\Controllers;

use App\Models\MotorForm;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Http\Requests\StoreMotorFormRequest;
use App\Http\Requests\UpdateMotorFormRequest;

class MotorFormController extends Controller
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

        return view('forms.motor_form.index', compact('policy', 'policyId', 'customer'));
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
    public function store(StoreMotorFormRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MotorForm $motorForm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MotorForm $motorForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMotorFormRequest $request, MotorForm $motorForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MotorForm $motorForm)
    {
        //
    }
}
