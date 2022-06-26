<?php

namespace App\Http\Requests\Admin\Misc;

use Illuminate\Foundation\Http\FormRequest;

class BannerCreateRequest extends FormRequest
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
            // 'banner' => 'array|required',
            // 'banner.*' => 'required',
            'banner' => 'required',
            'desc_id' => 'required',
        ];
    }
}
