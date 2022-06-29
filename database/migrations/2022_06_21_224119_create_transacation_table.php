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
        Schema::create('transacation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->biginteger('user_id');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone');

            $table->string('user_address');
            // $table->bigInteger('program_id');
            // $table->string('program_name');
            // $table->bigInteger('ticket_id');
            // $table->string('ticket_name');
            // $table->string('ticket_price_idr');
            // $table->string('ticket_price_usd');

            $table->bigInteger('voucher_id');
            $table->string('voucher_code');

            $table->string('discount_value');
            $table->decimal('gross_value', 25, 15);
            $table->decimal('net_value', 25, 15);

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
