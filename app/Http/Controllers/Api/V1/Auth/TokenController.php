<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Http\Resources\Api\V1\StaffResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Dedoc\Scramble\Attributes\Group;

#[Group('Authentication', weight: 1)]
class TokenController extends Controller
{
    public function me(Request $request)
    {
        $actor = $request->user();

        return $actor instanceof Customer ? new CustomerResource($actor) : new StaffResource($actor);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->noContent();
    }
}
