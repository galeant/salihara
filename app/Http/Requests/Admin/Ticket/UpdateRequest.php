<?php

namespace App\Http\Requests\Admin\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use App\Ticket;
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
            'type' => 'required|in:' . implode(',', Ticket::type),
            'name' => 'required|unique:ticket,name,' . $this->id,
            'program_id' => 'required|array',
            'program_id.*' => [
                'required',
                'exists:program,id',
                function ($att, $val, $fail) {
                    $count = Program::where([
                        'type' => $this->type,
                        'id' => $val
                    ])->count();
                    if ($count == 0) {
                        $fail('program type not same as ticket type');
                    }
                }
            ],
            'desc_id' => 'required',
        ];
        if ($this->type == Ticket::type[1]) {
            $return['external_url'] = 'required';
        } else if ($this->type == Ticket::type[0]) {
            $return['price_idr'] = 'required|numeric';
        }
        return $return;
    }
}
