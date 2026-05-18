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
        $policy   = Policy::where('external_policy_id', $policyId)->firstOrFail();
        $customer = Customer::findOrFail($policy->customer_id);

        // Pull motor risk details from raw_payload
        $raw        = $policy->raw_payload ?? [];
        $motorRisks = $raw['motor_risks'] ?? [];
        $firstRisk  = ! empty($motorRisks) ? (array) $motorRisks[0] : [];

        // Pre-populate form fields from GLIMS data
        $formData = [
            // Section 1 — Vehicle Particulars
            'registration_no' => $firstRisk['objecth_02_plate_number'] ?? '',
            'make'            => $firstRisk['objecth_02_make'] ?? '',
            'model'           => $firstRisk['objecth_02_model'] ?? '',
            'year_of_make'    => $firstRisk['objecth_02_year'] ?? '',

            // Section 2 — Driver Particulars (pre-fill with policy holder)
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
