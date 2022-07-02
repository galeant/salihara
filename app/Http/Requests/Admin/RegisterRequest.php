<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'phone' => 'required',
            'gender' => 'required|in:1,2,3',
            'birth_year' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'address' => 'required',
            'province_id' => 'required|exists:province,id',
            'city_id' => 'required|exists:city,id',
            'district_id' => 'required|exists:district,id',
            'sub_district_id' => 'required|exists:sub_district,id',
        ];
    }
}
