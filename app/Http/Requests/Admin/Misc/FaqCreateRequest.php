<?php

namespace App\Http\Requests\Admin\Misc;

use Illuminate\Foundation\Http\FormRequest;

class FaqCreateRequest extends FormRequest
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
            'group_id' => 'required',
            'group_en' => 'required',
            'question_id' => 'required',
            'question_en' => 'required',
            'answer_id' => 'required',
            'answer_en' => 'required',
        ];
    }
}
