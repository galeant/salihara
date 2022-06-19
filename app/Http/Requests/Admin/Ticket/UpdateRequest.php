<?php

namespace App\Http\Requests\Admin\Ticket;

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
            'name' => 'required',
            'program_id' => 'required|exists:program,id',
            'price_idr'  => 'required|numeric',
            'desc_id' => 'required',
        ];
    }
}
