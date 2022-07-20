<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use App\Ticket;

class CartRequest extends FormRequest
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
        $return =  [
            'ticket_id' => [
                'bail',
                'required',
                'exists:ticket,id',
                function ($attr, $val, $fail) {
                    $check = Ticket::where([
                        'id' => $val,
                        'type' => Ticket::type[1]
                    ])->count();
                    if ($check != 0) {
                        $fail('Ticket is not type daring');
                    }
                }
            ]
        ];

        if ($this->route()->uri == 'customer/remove_cart') {
            unset($return['ticket_id'][3]);
        }
        return $return;
    }
}
