<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\UserStoreResponseResource;
use App\Http\Resources\V1\UserLoginResponseResource;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\V1\ApiController;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.role:system-admin,user', ['except' => ['register', 'login', 'userProfile']]);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(StoreUserRequest $request) {
        $user_id = Str::uuid()->toString();

        if ($request->role == "user") {
            Customer::create([
                'user_id' => $user_id
            ]);
        }

        $user = User::create(array_merge([
            'id' => $user_id,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'registration_at' => Carbon::now()->timestamp,
            'role' => $request->role])
        );

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new UserStoreResponseResource($user),
            Response::HTTP_OK
        );
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request){
        if (! $token = JWTAuth::attempt($request->validated())) {
            return response()->json([
                'message' => 'Invalid Email or Password',
                'errors' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->createNewToken($token);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
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
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        $user_id = auth()->user()->id;

        if ($user_id != null) {
            auth()->logout();
            return $this->successResponse(
                Response::HTTP_OK . " OK",
                "User " . $user_id . " successfully signed out",
                Response::HTTP_OK
            );
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        $tokenPack = (object)[
            'user_id' => auth()->user()->id,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() . " Minutes",
        ];

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new UserLoginResponseResource($tokenPack),
            Response::HTTP_OK
        );
    }
}
