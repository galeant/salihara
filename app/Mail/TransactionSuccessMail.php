<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Http\Payment;

class TransactionSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject, $url, $str_title,
        $str_button, $str, $reff_id, $transaction_date,
        $total_payment, $payment_method, $trans_detail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $transaction, $type)
    {
        $subject = 'Menunggu Pembayaran';
        $url = ENV('TRANSACTION_LIST_URL');
        $str_title = 'Menunggu Pembayaran';
        $str_button = 'Lihat Status Pesanan';
        $str = 'Hi ' . $user->name . ', Segera lakukan pembayaran sebelum <strong>( hari ini + 1 hari) pukul 13.00&nbsp;</strong>atau pesanan anda akan di batalkan secara otomatis.';

        $transaction_date = Carbon::parse($transaction->created_at)->setTimezone('Asia/Jakarta')->isoFormat('dddd, D MMMM Y hh:mm') . ' WIB';
        $payment_method = NULL;
        if ($transaction->payment_method_id !== NULL && $transaction->payment_method_id !== '') {
            $payment_list = collect(Payment::PAYMENT_METHOD);
            $payment_method = $payment_list->first(function ($v) use ($transaction) {
                if ($v['id'] == $transaction->payment_method_id) {
                    return $v;
                }
            });
            $payment_method = isset($payment_method) ? $payment_method['name'] : NULL;
        }


        $trans_detail = collect($transaction->detail)->transform(function ($v) {
            $str_product = '<b>' . $v->ticket_name . '</b><ul>';
            foreach ($v->ticket->program as $tcp) {
                // $tmp_str_product = '<li><b>' . $tcp->name . '</b><ul>';
                // $sch = dd($tcp->schedule);
                // foreach ($tcp->schedule as $sch) {
                //     $tmp_str_product = $tmp_str_product . '<li>' . Carbon::parse($sch->unix_date)->setTimezone('Asia/Jakarta')->format('Y-m-d') . '</li>';
                // }
                // $tmp_str_product = $tmp_str_product . '</ul></li>';
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
                'product' => 'Total',
                'qty' => NULL,
                'price' => $trans_detail->sum('price')
            ]
        );

        $trans_detail->transform(function ($v) {
            $v['price'] = number_format($v['price'], 0, ',', '.');
            return $v;
        });

        if ($type == 'payment_success') {
            $subject = 'Pembayaran Berhasil';
            $detail_first = $transaction->detail->first();
            $program_first = $detail_first->ticket->program->first();
            $url = ENV('PROGRAM_DETAIL_URL');
            $url = str_replace('{slug}', $program_first->slug, $url);

            $str_title = 'Pembayaran Berhasil';
            $str_button = 'Tonton Sekarang';
            $str = 'Hi ' . $user->name . ', Pembayaran Anda telah berhasil. Klik tombol dibawah untuk memulai menonton.';
        }

        $this->subject = $subject;
        $this->url = $url;
        $this->str_title = $str_title;
        $this->str_button = $str_button;
        $this->str = $str;
        $this->reff_id = $transaction->reff_id;
        $this->transaction_date = $transaction_date;
        $this->total_payment = number_format($transaction->net_value_idr, 0, ',', '.');
        $this->payment_method = $payment_method;
        $this->trans_detail = $trans_detail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view('email.transaction');
    }
}
