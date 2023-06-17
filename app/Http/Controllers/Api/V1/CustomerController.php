<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Http\Resources\V1\CustomerResource;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($username)
    {
        $user = User::where('username', $username)->first();
        // dd($user);
        if ($user === null) {
            return response()->json([
                'errors' => 'Customer with this username is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $customer = Customer::where('user_id', $user->id)->first();
            return new CustomerResource($customer);
        }
    }
}
