<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class RoleAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            //Access token from the request and Try authenticating
            // $token = JWTAuth::parseToken();
            $user = JWTAuth::parseToken()->authenticate();
            //Try authenticating user
            // $user = $token->authenticate();
        } catch (TokenExpiredException $e) {
            //Thrown if token has expired
            return response()->json([
                'status'=> Response::HTTP_UNAUTHORIZED . ' Unauthorized',
                'message' => 'Your token has expired. Please, login again',
                'errors' => 'Your token has expired. Please, login again',
                'data' => null
            ], Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            //Thrown if token invalid
            return response()->json([
                'status'=> Response::HTTP_UNAUTHORIZED . ' Unauthorized',
                'message' => 'Your token is invalid. Please, login again',
                'errors' => 'Your token is invalid. Please, login again',
                'data' => null
            ], Response::HTTP_UNAUTHORIZED);
        }catch (JWTException $e) {
            //Thrown if token was not found in the request.
            return response()->json([
                'status'=> Response::HTTP_UNAUTHORIZED . ' Unauthorized',
                'message' => 'Please, attach a Bearer Token to your request',
                'errors' => 'Please, attach a Bearer Token to your request',
                'data' => null
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user || !$this->checkRoles($user, $roles)) {
            return $this->unauthorized();
        }

        return $next($request);

        //If user was authenticated successfully and user is in one of the acceptable roles, send to next request.
        // if ($user && in_array($user->role, $roles)) {
        //     return $next($request);
        // }

        // return $this->unauthorized();
    }

    private function checkRoles($user, $roles)
    {
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return true;
            }
        }

        return false;
    }

    private function unauthorized($message = null){
        return response()->json([
            'status'=> Response::HTTP_UNAUTHORIZED . ' Unauthorized',
            'message' => $message ? $message : 'You are unauthorized to access this resource',
			'errors' => $message ? $message : 'You are unauthorized to access this resource',
			'data' => null
        ], Response::HTTP_UNAUTHORIZED);
    }
}
