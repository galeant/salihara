<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Support\Facades\Route;
use DB;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $uri = Route::current()->uri;
        $customer = str_starts_with($uri, 'customer');
        $admin = str_starts_with($uri, 'admin');

        try {
            $user = User::when($customer == true, function ($q) {
                $q->customer();
            })->when($admin == true, function ($q) {
                $q->admin();
            })->where([
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

    public function test()
    {
        $data = DB::table('recurve')->get();
        // $c = $this->buildTree($data);
        $d = $this->buildtier($data);
        dd($d);
    }

    private function buildTree($elements, $parent_id  = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element->parent_id == $parent_id) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    private function buildtier($data, $parent_id = [])
    {
        $return = [];

        // dd($return);

        if (count($parent_id) == 0) {
            $ap_return = collect($data)->filter(function ($v) {
                if ($v->parent_id == NULL) {
                    return $v;
                }
            });
            // dd($ap_return);
            $return[] = $ap_return;
            $parent_id = $ap_return->pluck('id');
        } else {
            $this->buildtier($data, $parent_id);
        }
        dd($return);
        dd('qwdqdq');
    }
}
