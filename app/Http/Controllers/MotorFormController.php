<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreMotorFormRequest;
use App\Http\Requests\UpdateMotorFormRequest;
use App\Models\Customer;
use App\Models\MotorForm;
use App\Models\Policy;
use Illuminate\Http\Request;

class MotorFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $policyId = $request->query('policyId');
        $policy = Policy::where('external_policy_id', $policyId)->orWhere('id', $policyId)->firstOrFail();
        $customer = Customer::findOrFail($policy->customer_id);

        // Risks are keyed by risk ID — grab the first one
        $risks     = $policy->raw_payload['risks'] ?? [];
        $firstRisk = collect($risks)->first() ?? [];

        $formData = [
            'registration_no' => $firstRisk['risk_ref_no'] ?? '',
            'make'            => $firstRisk['vehicle_make'] ?? '',
            'model'           => $firstRisk['vehicle_model'] ?? '',
            'year_of_make'    => $firstRisk['vehicle_yr_manufacture'] ?? '',
            'chassis_no'      => $firstRisk['vehicle_chassis_no'] ?? '',
            'colour'          => $firstRisk['vehicle_colour'] ?? '',
            'body_type'       => $firstRisk['vehicle_body_type'] ?? '',
            'seating'         => $firstRisk['vehicle_seating'] ?? '',
            'fullname'        => $customer->name ?? '',
            'phone'           => $customer->phone ?? '',
        ];

        return view('forms.motor_form.index', compact('policy', 'policyId', 'customer', 'formData'));
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
