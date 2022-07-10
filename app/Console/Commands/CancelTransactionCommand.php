<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Transaction;
use App\PaymentLog;
use App\Http\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CancelTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel transaction who has been payment expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Log::info('bebek');
        // dd(Carbon::parse(1657472220)->format('Y-m-d H:i:s'));
        $now = time();
        // $now = 1657472221;
        $data = Transaction::where('payment_status', Payment::PAYMENT_STATUS[0])
            ->where('epoch_time_payment_expired', '<', $now)
            ->get();

        if (count($data) > 0) {
            foreach ($data as $dt) {
                Log::channel('payment_log')->info('begin:' . json_encode($dt->toArray()));
                // dd($dt);
                $dt->update([
                    'payment_status' => Payment::PAYMENT_STATUS[2]
                ]);

                PaymentLog::firstOrCreate([
                    'transaction_id' => $dt->id,
                    'status' => Payment::PAYMENT_STATUS[2],
                ], [
                    'payload_request' => 'Payment Expired',
                    'payload_response' => 'Payment Expired'
                ]);

                Log::channel('payment_log')->info('end:' . json_encode($dt->fresh()->toArray()));
            }
        }
    }
}
