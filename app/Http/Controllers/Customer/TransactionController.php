<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Image;
use App\Http\Response\Customer\TransactionTransformer;
use App\Cart;

use Illuminate\Support\Facades\Log;
use App\Http\Requests\Customer\CheckoutRequest;
use App\Http\Requests\Customer\CartRequest;
use App\Ticket;
use App\Voucher;
use DB;
use Carbon\Carbon;
use App\Transaction;
use App\TransactionDetail;
use App\Http\Payment;

class TransactionController extends Controller
{

    public function cart(Request $request)
    {
        $user = auth()->user();
        try {
            $data = Cart::with('user', 'ticket.program')->where('user_id', $user->id)->get();
            return TransactionTransformer::cart($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function addCart(CartRequest $request)
    {
        DB::beginTransaction();
        $user = auth()->user();
        try {
            $data = Cart::where([
                'user_id' => $user->id,
                'ticket_id' => $request->ticket_id
            ])->first();
            if ($data !== NULL) {
                // $data->update([
                //     'qty' => ($data->qty + 1)
                // ]);
            } else {
                $data = Cart::create([
                    'user_id' => $user->id,
                    'ticket_id' => $request->ticket_id,
                    'qty' => 1
                ]);
            }
            DB::commit();
            return $this->cart($request);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function checkout(CheckoutRequest $request)
    {
        // dd('ini checkout');
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $voucher_id = NULL;
            $voucher_code = NULL;
            $voucher_discount = 0;
            if ($request->filled('voucher_code')) {
                $voucher = Voucher::where('code', $request->voucher_code)->firstOrFail();
                $voucher_id = $voucher->id;
                $voucher_code = $voucher->code;
                $voucher_discount = $voucher->discount;
                $voucher->update([
                    'used_quota' => $voucher->used_quota + 1
                ]);
            }
            $gross_value_idr = 0;
            $gross_value_usd = 0;
            $trans_detail = [];
            foreach ($request->cart as $cart) {
                $ticket = Ticket::with('program')->where('id', $cart['ticket_id'])->first();
                if ($ticket !== NULL) {
                    $val_idr = $ticket->price_idr * $cart['qty'];
                    $val_usd = $ticket->price_usd * $cart['qty'];
                    $gross_value_idr = $gross_value_idr + $val_idr;
                    $gross_value_usd = $gross_value_usd + $val_usd;
                    $trans_detail[] = [
                        'program_id' => $ticket->program_id,
                        'program_name' => $ticket->program->name,
                        'ticket_id' => $ticket->id,
                        'ticket_name' => $ticket->name,
                        'ticket_price_idr' => $ticket->price_idr,
                        'ticket_price_usd' => $ticket->price_usd,
                        'qty' => $cart['qty'],
                        'total_price_idr' => $val_idr,
                        'total_price_usd' => $val_usd,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
            $nett_idr = $gross_value_idr - (float)$voucher_discount;
            $nett_usd = $gross_value_usd - (float)$voucher_discount;
            $payment_method = collect(Payment::PAYMENT_METHOD)->first(function ($v) use ($request) {
                if ($v['id'] == $request->payment_method_id) {
                    return $v;
                }
            });
            $trans_fill = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $user->phone,
                'user_address' => $user->address,

                'province_id' => $user->province_id,
                'province_name' => $user->province->name_id,

                'city_id' => $user->city_id,
                'city_name' => $user->city->name_id,

                'district_id' => $user->district_id,
                'district_name' => $user->district->name_id,

                'sub_district_id' => $user->sub_district_id,
                'sub_district_name' => $user->subDistrict->name_id,
                'postal' => $user->subDistrict->postal,

                'voucher_id' => $voucher_id,
                'voucher_code' => $voucher_code,
                'discount_value' => $voucher_discount,

                'gross_value_idr' => $gross_value_idr,
                'gross_value_usd' => $gross_value_usd,

                'net_value_idr' => $nett_idr,
                'net_value_usd' => $nett_usd,

                'payment_method_id' => $request->payment_method_id,
                'payment_method_name' => $payment_method['name'],
                'payment_status' => Payment::PAYMENT_STATUS[0],
            ];

            $payment_gateway = (new Payment)->paymentRequest($trans_fill, $trans_detail);
            // $payment_gateway = (new Payment)->paymentRequestBeta($trans_fill, $trans_detail);
            $trans_fill['signature_payment'] = $payment_gateway->Signature;
            $trans_fill['checkout_id'] = $payment_gateway->CheckoutID;
            $trans_fill['payment_expired'] = Carbon::parse($payment_gateway->TransactionExpiryDate);
            $trans_fill['epoch_time_payment_expired'] = strtotime($payment_gateway->TransactionExpiryDate);
            $trans_fill['virtual_account_assign'] = $payment_gateway->VirtualAccountAssigned;
            $trans_fill['reff_id'] = $payment_gateway->RefNo;
            $transaction = Transaction::create($trans_fill);
            foreach ($trans_detail as $tr_detail) {
                $tr_detail['transaction_id'] = $transaction->id;
                TransactionDetail::create($tr_detail);
            }
            DB::commit();
            $data = Transaction::where('id', $transaction->id)->first();
            return TransactionTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function transaction(Request $request)
    {
        try {
            $user = auth()->user();
            $data = Transaction::with('detail')->where('user_id', $user->id);
            if ($request->filled('reff_id')) {
                $data = $data->where('reff_id', $request->reff_id)->first();
                return TransactionTransformer::getDetail($data);
            }
            $data = $data->get();
            return TransactionTransformer::getList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function paymentMethod(Request $request)
    {
        try {
            $list = Payment::PAYMENT_METHOD;
            return TransactionTransformer::paymentMethodList($list);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function paymentTest(Request $request)
    {
        Log::info(json_encode($request->all()));
        dd('ini url penerima');
    }

    public function paymentRedirect(Request $request)
    {
        Log::info(json_encode($request->all()));
        dd('ini url redirect');
    }
}
