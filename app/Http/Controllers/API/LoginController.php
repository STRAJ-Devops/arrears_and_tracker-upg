<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $attributes = request()->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('officer')->attempt($attributes)) {
            $user = Officer::where('username', $request->username)->first();

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
                'user' => $user,
            ]);
        } else {
            //look for the user in the database
            $officer = Officer::where('username', request('username'))->first();

            if ($officer) {
                //is password correct, just compare 2 strings
                if (request('password') == $officer->un_hashed_password) {
                    $token = $officer->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'authorization' => [
                            'token' => $token,
                            'type' => 'bearer',
                        ],
                        'user' => $officer,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Username or password invalid.',
                        'status' => 'error',
                    ], 401);
                }
            }

            return response()->json([
                'message' => 'Username or password invalid.',
                'status' => 'error',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $logout = $request->user()->currentAccessToken()->delete();
        if (!$logout) {
            return response()->json([
                'message' => 'Logout failed',
                'status' => 'error',
            ], 500);
        }

        return response()->json([
            'message' => 'Logged out',
            'status' => 'success',
        ], 200);
    }
}
