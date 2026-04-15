<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {

        return view('staff.dashboard.index');
    }

    public function allClaims()
    {
        return view('staff.all-claims.index');
    }
    
    public function myClaims()
    {
        return view('staff.my-claims.index');
    }

    public function claimForms()
    {
        return view('staff.claim-forms.index');
    }

    public function createClaimForms()
    {
        return view('staff.claim-forms.create');
    }

    public function claimDouments()
    {
        return view('staff.claim-documents.index');
    }

    public function customers()
    {
        $customers = Customer::latest()->paginate(10);
        return view('staff.customers.index', compact('customers'));
    }

    public function settings()
    {
        return view('staff.settings.index');
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
