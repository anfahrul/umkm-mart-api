<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\CustomerUpdateResponseResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Controllers\Api\V1\ApiController;

class CustomerController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.role:user', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index($username)
    {
        $user = User::where('username', $username)->first();

        if ($user === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Username " . $username . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            $customer = Customer::where('user_id', $user->id)->first();

            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new CustomerResource($customer),
                Response::HTTP_OK
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request)
    {
        $user = auth()->user();

        $customer = Customer::where('user_id', $user->id)->first();

        $customer->fullname = $request->fullname;
        $customer->phone_number = $request->phone_number;
        $customer->state = $request->state;
        $customer->province = $request->province;
        $customer->city = $request->city;
        $customer->postal_code = $request->postal_code;
        $customer->address = $request->address;

        $customer->save();
        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new CustomerUpdateResponseResource($customer),
            Response::HTTP_OK
        );
    }
}
