<?php

namespace App\Http\Requests\Admin\Misc;

use Illuminate\Foundation\Http\FormRequest;

class AboutCreateRequest extends FormRequest
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
            'desc_id' => 'required',
            'title_id' => 'required',
            'sub_title_id' => 'required',
        ];
    }
}
