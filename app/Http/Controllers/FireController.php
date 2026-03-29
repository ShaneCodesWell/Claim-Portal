<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFireRequest;
use App\Http\Requests\UpdateFireRequest;
use App\Models\Fire;

class FireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('fire_form.index');
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
