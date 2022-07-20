<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Payment;
use App\Cart;

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
        // $payment_method = array_column(Payment::PAYMENT_METHOD, 'id');
        // $payment_method = implode(',', $payment_method);
        $user = auth()->user();
        $cart = Cart::select('ticket_id')->where('user_id', $user->id)->get();
        $cart = $cart->pluck('ticket_id')->toArray();

        $return = [
            'cart' => 'required|array',
            'cart.*.ticket_id' => [
                'required',
                'exists:ticket,id',
                function ($attr, $val, $fail) use ($cart) {
                    if (!in_array($val, $cart)) {
                        $fail('Ticket not exist in cart');
                    }
                }
            ],
            'cart.*.qty' => 'required',
            'voucher_code' => 'nullable|exists:voucher,code',
            // 'payment_method_id' => 'required|in:' . $payment_method
        ];
        return $return;
    }
}
