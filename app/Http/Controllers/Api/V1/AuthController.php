<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Controllers\Controller;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(StoreUserRequest $request) {
        return new UserResource(
            User::create(array_merge([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)])
            )
        );
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request){
        if (! $token = auth()->attempt($request->validated())) {
            return response()->json([
                'message' => 'Invalid Email or Password',
                'errors' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->createNewToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return new UserResource(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => new UserResource(auth()->user())
        ]);
    }
}
