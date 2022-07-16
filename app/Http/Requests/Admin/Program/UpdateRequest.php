<?php

namespace App\Http\Requests\Admin\Program;

use Illuminate\Foundation\Http\FormRequest;
use App\Program;

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
        $return = [
            'name' => 'required|unique:program,name,' . $this->id,
            'schedule_date'  => 'required|date',
            'duration_hour' => 'required',
            'duration_minute' => 'nullable',
            'desc_id' => 'required',
            'only_indo' => 'nullable|in:' . true . ',' . false,
            'penampil_id' => 'array',
            'type' => 'required|in:' . implode(',', Program::type),
            'category' => 'required',
            'trailer_url' => 'required',
            'video_url' => 'required',
            'color' => 'required',
        ];
        if ($this->type == Program::type[1]) {
            $return['luring_url'] = 'required';
        }

        return $return;
    }
}
