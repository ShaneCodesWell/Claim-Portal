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
        $riskId   = $request->query('riskId');

        $policy   = Policy::where('external_policy_id', $policyId)->orWhere('id', $policyId)->firstOrFail();
        $customer = Customer::findOrFail($policy->customer_id);

        // Risks are keyed by risk ID — grab the first one
        $risks = $policy->raw_payload['risks'] ?? [];
        $firstRisk = collect($risks)->first() ?? [];

        // If riskId is present (fleet), find that specific risk — else use the first
        $risk = ($riskId && isset($risks[$riskId]))
            ? $risks[$riskId]
            : (collect($risks)->first() ?? []);

        $formData = [
            'registration_no' => $risk['risk_ref_no'] ?? '',
            'make'            => $risk['vehicle_make'] ?? '',
            'model'           => $risk['vehicle_model'] ?? '',
            'year_of_make'    => $risk['vehicle_yr_manufacture'] ?? '',
            'chassis_no'      => $risk['vehicle_chassis_no'] ?? '',
            'colour'          => $risk['vehicle_colour'] ?? '',
            'body_type'       => $risk['vehicle_body_type'] ?? '',
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
