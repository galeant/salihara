<?php

namespace App\Http\Controllers;

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
use App\User;
use App\PaymentLog;
use Illuminate\Support\Facades\Mail;

use App\Mail\TransactionSuccessMail;
use App\Http\Requests\Customer\CheckVoucherRequest;

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
            // $access = $user->access->pluck('id')->toArray();
            // if (in_array($request->ticket_id, $access)) {
            //     throw new \Exception('Ticket has already buy');
            // }
            // $transaction = Transaction::where('user_id', $user->id)
            //     ->where('payment_status', Payment::PAYMENT_STATUS[1])
            //     ->whereHas('detail', function ($q) use ($request) {
            //         $q->where('ticket_id', $request->ticket_id);
            //     })->count();

            // if ($transaction > 0) {
            //     throw new \Exception('Payment in proggress');
            // }

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

    public function removeCart(CartRequest $request)
    {
        DB::beginTransaction();
        $user = auth()->user();
        try {
            $data = Cart::where([
                'user_id' => $user->id,
                'ticket_id' => $request->ticket_id
            ])->first();
            if ($data !== NULL) {
                $data->delete();
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
                    'used_qouta' => ($voucher->used_qouta + 1),
                    'quota' => ($voucher->quota - 1)
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
                        // 'program_id' => $pr->id,
                        // 'program_name' => $pr->name,
                        // 'program_schedule' => json_encode($pr->schedule),
                        // 'program_slug' => $pr->slug,
                        // 'program_banner' => (isset($pr->imageBanner) && isset($pr->imageBanner->path)) ? url($pr->imageBanner->path) : NULL,
                        'ticket_id' => $ticket->id,
                        'ticket_name_id' => $ticket->name_id,
                        'ticket_name_en' => $ticket->name_en,
                        'ticket_price_idr' => $ticket->price_idr,
                        'ticket_price_usd' => $ticket->price_usd,
                        'qty' => $cart['qty'],
                        'total_price_idr' => $val_idr,
                        'total_price_usd' => $val_usd,
                    ];
                }
            }
            $nett_idr = $gross_value_idr - (float)$voucher_discount;
            $nett_usd = $gross_value_usd - (float)$voucher_discount;
            if ($nett_idr < 0) {
                $nett_idr = 0;
            }
            if ($nett_usd < 0) {
                $nett_usd = 0;
            }
            // $payment_method = collect(Payment::PAYMENT_METHOD)->first(function ($v) use ($request) {
            //     if ($v['id'] == $request->payment_method_id) {
            //         return $v;
            //     }
            // });


            $trans_fill = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $user->phone,
                // 'user_address' => $user->address,

                // 'province_id' => $user->province_id,
                // 'province_name' => $user->province->name_id,

                // 'city_id' => $user->city_id,
                // 'city_name' => $user->city->name_id,

                // 'district_id' => $user->district_id,
                // 'district_name' => $user->district->name_id,

                // 'sub_district_id' => $user->sub_district_id,
                // 'sub_district_name' => $user->subDistrict->name_id,
                // 'postal' => $user->subDistrict->postal,

                'voucher_id' => $voucher_id,
                'voucher_code' => $voucher_code,
                'discount_value' => $voucher_discount,

                'gross_value_idr' => $gross_value_idr,
                'gross_value_usd' => $gross_value_usd,

                'net_value_idr' => $nett_idr,
                'net_value_usd' => $nett_usd,

                'payment_method_id' => $request->payment_method_id,
                // 'payment_method_name' => $payment_method['name'],
                'payment_status' => Payment::PAYMENT_STATUS[1],
            ];

            // if ($nett_idr > 0) {
            $payment_gateway = (new Payment)->paymentRequest($trans_fill, $trans_detail);
            // $payment_gateway = (new Payment)->paymentRequestBeta($trans_fill, $trans_detail);

            $trans_fill['signature_payment'] = $payment_gateway->Signature;
            // $trans_fill['signature_payment'] = '';

            $trans_fill['checkout_id'] = $payment_gateway->CheckoutID;
            // $trans_fill['checkout_id'] = '';

            // $trans_fill['payment_expired'] = Carbon::parse($payment_gateway->TransactionExpiryDate);
            // $trans_fill['epoch_time_payment_expired'] = strtotime($payment_gateway->TransactionExpiryDate);
            // $trans_fill['virtual_account_assign'] = $payment_gateway->VirtualAccountAssigned;

            $trans_fill['reff_id'] = $payment_gateway->RefNo;
            // }

            $transaction = Transaction::create($trans_fill);

            foreach ($trans_detail as $tr_detail) {
                $tr_detail['transaction_id'] = $transaction->id;
                TransactionDetail::create($tr_detail);
            }

            PaymentLog::firstOrCreate([
                'transaction_id' => $transaction->id,
                'status' => Payment::PAYMENT_STATUS[1],
            ], [
                'payload_request' => json_encode($trans_fill),
                'payload_response' => json_encode($payment_gateway)
            ]);

            foreach ($request->cart as $crt) {
                Cart::where([
                    'user_id' => $user->id,
                    'ticket_id' => $crt['ticket_id']
                ])->delete();
            }

            Mail::to($user->email)->queue(new TransactionSuccessMail($user, $transaction->fresh(), 'checkout'));

            DB::commit();
            $data = Transaction::where('id', $transaction->id)->first();
            return TransactionTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function transaction(Request $request, $id = NULL)
    {
        $order_by = $request->input('order_by', ['created_at']);
        $sort = $request->input('sort', ['desc']);

        $search_by = $request->search_by;
        $keyword = $request->keyword;

        try {
            $user = auth()->user();
            if ($request->has('user')) {
                $user = $request->user;
            }
            $data = Transaction::with('customer', 'paymentLog', 'detail.ticket.program')->when($user !== NULL && $user->role !== User::ROLE[0], function ($q) use ($user, $request) {
                $q->where('user_id', $user->id);
            });
            if ($request->filled('reff_id') || $id !== NULL) {
                $key = 'reff_id';
                $val = $request->reff_id;
                if ($id !== NULL) {
                    $key = 'id';
                    $val = $request->id;
                }
                $data = $data->where($key, $val)->firstOrFail();
                return TransactionTransformer::getDetail($data);
            }
            $data = $data->order($order_by, $sort)
                ->search($search_by, $keyword);

            if ($request->has('all') || $request->all == true) {
                $data = $data->get();
            } else {
                $data = $data->paginate(10);
            }
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

    public function backendUrl(Request $request)
    {
        Log::channel('payment_log')->info('backendUrl log: ' . json_encode($request->all()));
        $msg = [
            '1' => [
                'Indonesian' => 'Pembayaran diterima',
                'English' => 'Status Received',
            ],
            '6' => [
                'Indonesian' => 'Pembayaran di tangguhkan',
                'English' => 'Status Pending',
            ],
            '0' => [
                'Indonesian' => 'Pembayaran gagal',
                'English' => 'Status failed',
            ]
        ];
        DB::beginTransaction();
        try {
            $data = Transaction::with('detail')->where('reff_id', $request->RefNo)
                ->where('payment_status', Payment::PAYMENT_STATUS[1])
                ->first();
            if ($data != NULL) {
                switch ($request->TransactionStatus) {
                    case '1':
                        $payment_status = Payment::PAYMENT_STATUS[0];
                        $exist_access = $data->customer->access->pluck('id')->toArray();

                        $transaction_ticket = $data->detail->groupBy('ticket_id')->keys()->toArray();
                        $final_access = array_diff($transaction_ticket, $exist_access);
                        $data->customer->access()->attach($final_access);

                        $expired_date = NULL;
                        $epoch_time_payment_expired = NULL;
                        if (isset($request->TransactionExpiryDate)) {
                            $expired_date = Carbon::parse($request->TransactionExpiryDate)->addHours(-7);
                            $epoch_time_payment_expired = strtotime($expired_date);
                        }

                        $payment_method_name = NULL;
                        if (isset($request->PaymentId)) {
                            $payment_list = Payment::PAYMENT_METHOD;
                            $payment_first = collect($payment_list)->first(function ($v) use ($request) {
                                if ($v['id'] == $request->PaymentId) {
                                    return $v;
                                }
                            });
                            if ($payment_first !== NULL) {
                                $payment_method_name = $payment_first['name'];
                            }
                        }

                        $data->update([
                            'payment_method_id' => isset($request->PaymentId) ? $request->PaymentId : NULL,
                            'payment_method_name' => $payment_method_name,

                            'payment_gateway_trans_id' => isset($request->TransId) ? $request->TransId : NULL,

                            'payment_expired' => $expired_date,
                            'epoch_time_payment_expired' => $epoch_time_payment_expired,
                            'virtual_account_assign' => isset($request->VirtualAccountAssigned) ? $request->VirtualAccountAssigned : NULL,
                        ]);

                        break;
                    case '6':
                        $payment_status = Payment::PAYMENT_STATUS[1];
                        break;
                    case '0':
                        $payment_status = Payment::PAYMENT_STATUS[2];
                        break;
                }
                if (!isset($payment_status)) {
                    header('Location: ' . ENV('TRANSACTION_LIST_URL'));
                    exit();
                }
                $data->update([
                    'payment_status' => $payment_status
                ]);
                PaymentLog::firstOrCreate([
                    'transaction_id' => $data->id,
                    'status' => $payment_status,
                ], [
                    'payload_request' => 'Payment Notification',
                    'payload_response' => json_encode($request->all()),
                ]);
                //

                //
                Log::channel('payment_log')->info('reponseUrl log: data ketemu' . json_encode($request->all()));
                if ($request->TransactionStatus == 1) {
                    Mail::to($data->user_email)->queue(new TransactionSuccessMail($data->customer, $data->fresh(), 'payment_success'));
                }
            } else {
                Log::channel('payment_log')->info('reponseUrl log: data tidak ditemukan' . json_encode($request->all()));
            }
            // Log::channel('payment_log')->info('backendUrl log: data tidak ketemu' . json_encode($request->all()));
            DB::commit();
            return response()->json([
                'code' => $request->TransactionStatus,
                'message' => $msg[$request->TransactionStatus]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('payment_log')->info('reponseUrl log: error' . json_encode($request->all()) . '||' . $e->getMessage());
            return response()->json([
                'code' => $request->TransactionStatus,
                'message' => $e->getMessage()
            ], 200);
            // throw new \Exception($e->getMessage());
        }
    }

    public function responseUrl(Request $request)
    {
        Log::channel('payment_log')->info('responseUrl log: ' . json_encode($request->all()));
    }

    public function checkVoucher(CheckVoucherRequest $request)
    {
        $user = auth()->user();
        try {
            $voucher = Voucher::where('code', $request->voucher_code)
                ->first();

            if ($voucher == NULL) {
                throw new \Exception('Voucer Not found');
            } else if ($voucher->quota == $voucher->used_qouta) {
                throw new \Exception('Voucer limit reached');
            }

            $ticket = array_column($request->cart, 'ticket_id');
            $data = Cart::with('user', 'ticket.program')
                ->where('user_id', $user->id)
                ->whereIn('ticket_id', $ticket)
                ->get();

            $ticket_list = $data->pluck('ticket');
            $discount = $voucher->discount;
            $sub_total = $ticket_list->sum('price_idr');
            $total = $ticket_list->sum('price_idr') - $discount;
            if ($total < 0) {
                $total = 0;
            }
            return TransactionTransformer::cart($data, $sub_total, $total, $discount);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    // public function redirect(Request $request)
    // {
    //     $res = (new Payment)->redirectRequest([
    //         'CheckoutID' => '6bb13baf8dc8149cf9daa52540f0d27580ce87c6fa5bf21ec8e20a3a809e89c4',
    //         'Signature' => 'fe567a0f586713d5690852876d0f3c79e1d6346d903309ee912c08910dbe328e'
    //     ]);
    //     echo $res;
    //     // dd($res);
    //     dd('badak');
    // }

    public function access(Request $request)
    {
        try {
            $user = auth()->user();
            $access = $user->access;
            return TransactionTransformer::access($access);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function emailTest(Request $request)
    {
        $data = Transaction::with('detail');
        if ($request->filled('id')) {
            $data = $data->where('id', $request->id);
        }
        $data = $data->first();

        $type = 'end';
        $url = ENV('TRANSACTION_LIST_URL');
        $str_title = 'Menunggu Pembayaran';
        $str_button = 'Lihat Status Pesanan';
        $str = 'Hi Adinda, Segera lakukan pembayaran sebelum <strong>( hari ini + 1 hari) pukul 13.00&nbsp;</strong>atau pesanan anda akan di batalkan secara otomatis.';

        $transaction_date = Carbon::parse($data->created_at)->setTimezone('Asia/Jakarta')->isoFormat('dddd, D MMMM Y hh:mm') . ' WIB';
        $payment_method = NULL;
        if ($data->payment_method_id !== NULL && $data->payment_method_id !== '') {
            $payment_list = collect(Payment::PAYMENT_METHOD);
            $payment_method = $payment_list->first(function ($v) use ($data) {
                if ($v['id'] == $data->payment_method_id) {
                    return $v;
                }
            });
            $payment_method = isset($payment_method) ? $payment_method['name'] : NULL;
        }


        $trans_detail = collect($data->detail)->transform(function ($v) {
            $str_product = '<b>' . $v->ticket_name_id . '</b><ul>';
            foreach ($v->ticket->program as $tcp) {
                $tmp_str_product = '<li><b>' . $tcp->name . '</b>';
                $tmp_str_product = $tmp_str_product . $tcp->schedule_id;
                $tmp_str_product = $tmp_str_product . '</li>';
                $str_product = $str_product . $tmp_str_product;
            }
            $str_product = $str_product . '</ul>';
            return [
                'product' => $str_product,
                'qty' => 'x' . $v->qty,
                'price' => $v->total_price_idr,
            ];
        });

        $trans_detail->push(
            [
                'product' => '<b>Total</b>',
                'qty' => NULL,
                'price' => $trans_detail->sum('price')
            ]
        );

        $trans_detail->transform(function ($v) {
            $v['price'] = number_format($v['price'], 0, ',', '.');
            return $v;
        });
        if ($type == 'end') {
            $detail_first = $data->detail->first();
            $program_first = $detail_first->ticket->program->first();
            $url = ENV('PROGRAM_DETAIL_URL');
            $url = str_replace('{slug}', $program_first->slug, $url);
            $str_title = 'Pembayaran Berhasil';
            $str_button = 'Tonton Sekarang';
            $str = 'Hi Adinda, Pembayaran Anda telah berhasil. Klik tombol dibawah untuk memulai menonton.';
        }
        return view('email.transaction', [
            'url' => $url,
            'str_title' => $str_title,
            'str_button' => $str_button,
            'str' => $str,
            'reff_id' => $data->reff_id,
            'transaction_date' => $transaction_date,
            'total_payment' => number_format($data->net_value_idr, 0, ',', '.'),
            'payment_method' => $payment_method,
            'trans_detail' => $trans_detail
        ]);
    }
}
