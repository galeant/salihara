<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('program_id');
            $table->integer('order');
            $table->string('name');
            $table->string('slug');
            $table->decimal('price_idr', 18, 6);
            $table->decimal('price_usd', 18, 6)->nullable();
            $table->text('desc_id');
            $table->text('desc_en')->nullable();

            $table->text('snk_id')->nullable();
            $table->text('snk_en')->nullable();

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
        Schema::dropIfExists('ticket');
    }
}
