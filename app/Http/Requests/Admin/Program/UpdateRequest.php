<?php

namespace App\Http\Requests\Admin\Program;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name' => 'required|unique:program,name',
            'schedule_date'  => 'required|date',
            'duration_hour' => 'required',
            'duration_minute' => 'nullable',
            'desc_id' => 'required',
            'only_indo' => 'nullable|in:' . true . ',' . false,
            'penampil_id' => 'array',
            'type' => 'required|in:daring,luring',
            'video_url' => 'required',
        ];
    }
}
