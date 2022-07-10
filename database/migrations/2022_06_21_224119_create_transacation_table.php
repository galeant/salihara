<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransacationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reff_id')->unique();
            $table->biginteger('user_id');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone');
            $table->string('user_address');

            $table->bigInteger('payment_method_id');
            $table->string('payment_method_name');
            $table->string('payment_status')->default('pending')->description('pending|paid|cancel');

            $table->bigInteger('province_id');
            $table->string('province_name');

            $table->bigInteger('city_id');
            $table->string('city_name');

            $table->bigInteger('district_id');
            $table->string('district_name');

            $table->bigInteger('sub_district_id');
            $table->string('sub_district_name');
            $table->string('postal');

            $table->bigInteger('voucher_id')->nullable();
            $table->string('voucher_code')->nullable();
            $table->string('voucher_discount')->nullable();

            $table->string('discount_value');
            $table->decimal('gross_value_idr', 25, 5);
            $table->decimal('gross_value_usd', 25, 5);
            $table->decimal('net_value_idr', 25, 5);
            $table->decimal('net_value_usd', 25, 5);

            $table->string('payment_gateway_trans_id')->nullable();
            $table->string('signature_payment');
            $table->string('checkout_id');
            $table->dateTime('payment_expired');
            $table->string('epoch_time_payment_expired');

            $table->string('virtual_account_assign')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transacation');
    }
}
