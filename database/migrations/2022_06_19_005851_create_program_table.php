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
            $table->string('category_id');
            $table->string('category_en')->nullable();
            $table->text('schedule_id')->nullable();
            $table->text('schedule_en')->nullable();
            // $table->string('schedule_unix')->comment('unix timestamp ');
            // $table->dateTime('schedule_date');
            // $table->integer('duration_hour')->default(0);
            // $table->integer('duration_minute')->default(0);

            $table->enum('type', ['daring', 'external']);

            $table->text('desc_id')->nullable();
            $table->text('desc_en')->nullable();

            $table->boolean('only_indo')->defaul(false);

            $table->text('luring_url')->nullable();

            $table->text('trailer_url')->nullable();
            $table->text('video_url')->nullable();

            $table->boolean('is_publish')->default(true);
            $table->string('color');

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
