<?php

namespace App\Http\Controllers;

use App\Models\MotorForm;
use App\Http\Requests\StoreMotorFormRequest;
use App\Http\Requests\UpdateMotorFormRequest;

class MotorFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('motor_form.index');
    }

    public function index2()
    {
        return view('general_accident_form.index');
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
