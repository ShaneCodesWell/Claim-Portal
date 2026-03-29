<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfflineRequest;
use App\Http\Requests\UpdateOfflineRequest;
use App\Models\Offline;

class OfflineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('offline.index');
    }

    public function motorForm()
    {
        return view('offline.motor-form');
    }

    public function generalAccidentForm()
    {
        return view('offline.general-accident-form');
    }

    public function fireForm()
    {
        return view('offline.fire-form');
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
    public function store(StoreOfflineRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Offline $offline)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offline $offline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfflineRequest $request, Offline $offline)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offline $offline)
    {
        //
    }
}
