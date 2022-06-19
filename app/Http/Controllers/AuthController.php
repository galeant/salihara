<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $user = User::admin()->where([
                'email' => $request->email,
            ])->firstOrFail();

            if (Hash::check($request->password, $user->password)) {
                $token = auth()->attempt([
                    'email' => $user->email,
                    'password' => $request->password
                ]);
                return response()->json([
                    'code' => 200,
                    'message' => 'Login Success',
                    'result' => [
                        'user' => $user,
                        'token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => auth()->factory()->getTTL() * 60
                    ]

                ]);
            }
            throw new \Exception('Wrong Password');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function profile()
    {
        try {
            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'result' => auth()->user()
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json([
                'code' => 200,
                'message' => 'Logout Success',
                'result' => NULL
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
