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
            'banner' => 'array|required',
            'banner.*.title_id' => 'required',
            'banner.*.title_en' => 'required',
            'banner.*.sub_title_id' => 'required',
            'banner.*.sub_title_en' => 'required',
            'banner.*.desc_id' => 'required',
            'banner.*.desc_en' => 'required',
            'banner.*.image' => 'required',
            'banner.*.hyperlink' => 'required',
            // 'banner' => 'required',
            // 'desc_id' => 'required',
            // 'title_id' => 'required',
            // 'sub_title_id' => 'required',
        ];
    }
}
