<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order');
            $table->string('name');
            $table->string('slug');
            $table->string('schedule_unix')->comment('unix timestamp ');
            $table->date('schedule_date');
            $table->integer('duration_hour')->default(0);
            $table->integer('duration_minute')->default(0);

            $table->enum('type', ['daring', 'luring']);

            $table->text('desc_id')->nullable();
            $table->text('desc_en')->nullable();

            $table->boolean('only_indo')->defaul(false);

            $table->text('video_url')->nullable();

            $table->boolean('is_publish')->default(true);

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
        Schema::dropIfExists('program');
    }
}
