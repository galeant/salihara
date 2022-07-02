<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UpdateProfileRequest extends FormRequest
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
        $uri = Route::current()->uri;
        $customer = str_starts_with($uri, 'customer');
        $rules =  [
            'name' => 'nullable',
            'password' => 'nullable',
            'confirm_password' => 'nullable|same:password',
        ];
        if ($customer) {
            $rules['phone'] = 'nullable';
            $rules['gender'] = 'nullable|in:1,2,3';
            $rules['birth_year'] = 'nullable';
            $rules['address'] = 'nullable';
            $rules['province_id'] = 'nullable|exists:province,id';
            $rules['city_id'] = 'nullable|exists:city,id';
            $rules['district_id'] = 'nullable|exists:district,id';
            $rules['sub_district_id'] = 'nullable|exists:sub_district,id';
        }
        return $rules;
    }
}
