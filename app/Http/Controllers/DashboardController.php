<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GenovaApiService;
class DashboardController extends Controller
{
    protected $api;

    public function __construct(GenovaApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $policiesResponse = $this->api->getPolicies(session('customer_code'));

        $policies = $policiesResponse->successful()
            ? $policiesResponse->json('data')
            : [];

        return view('dashboard.index', [
            'name'     => session('fullname'),
            'policies' => $policies,
        ]);
    }
}