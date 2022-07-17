<?php

namespace App\Http\Response;

use Carbon\Carbon;

class AuthTransformer
{

    public static function login($data, $token, $message = 'Success')
    {

        return response()->json([
            'message' => $message,
            'result' => [
                'user' => self::reformProfile($data),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ],
        ]);
    }

    public static function profile($data, $message = 'Success')
    {

        return response()->json([
            'message' => $message,
            'result' => self::reformProfile($data)
        ]);
    }

    private static function reformProfile($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            // 'address' => $data->address,
            'gender' => $data->gender,
            'birth_year' => $data->birth_year,
            // 'province_id' => $data->province_id,
            // 'city_id' => $data->city_id,
            // 'district_id' => $data->district_id,
            // 'sub_district_id' => $data->sub_district_id,
            // 'postal' => $data->subDistrict->postal,
        ];
    }
}
