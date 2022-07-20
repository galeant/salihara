<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Payment;

class CheckoutRequest extends FormRequest
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
        $payment_method = array_column(Payment::PAYMENT_METHOD, 'id');
        $payment_method = implode(',', $payment_method);

        return [
            'cart' => 'required|array',
            'cart.*.ticket_id' => [
                'required|exists:ticket,id',
            ],
            'cart.*.qty' => 'required',
            'voucher_code' => 'nullable|exists:voucher,code',
            'payment_method_id' => 'required|in:' . $payment_method
        ];
    }
}
