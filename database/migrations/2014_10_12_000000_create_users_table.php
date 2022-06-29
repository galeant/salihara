<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // $table->rememberToken();
            $table->enum('role', ['admin', 'customer']);

            $table->string('phone')->nullable();
            $table->text('address');

            $table->bigInteger('province_id');
            $table->bigInteger('city_id');
            $table->bigInteger('district_id'); //kelurahan di data source urban
            $table->bigInteger('sub_district_id'); //kecamatan

            $table->boolean('is_disabled')->default(false);

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
        Schema::dropIfExists('users');
    }
}
