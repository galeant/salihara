<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_detail', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('transaction_id');


            $table->bigInteger('program_id');
            $table->string('program_name');

            $table->bigInteger('ticket_id');
            $table->string('ticket_name');


            $table->string('ticket_price_idr');
            $table->string('ticket_price_usd')->nullable();

            $table->bigInteger('qty');

            $table->decimal('total_price_idr', 25, 5);
            $table->decimal('total_price_usd', 25, 5)->nullable();

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
        Schema::dropIfExists('transaction_detail');
    }
}
