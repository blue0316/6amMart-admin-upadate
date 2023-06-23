<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->string('f_name',100)->nullable();
            $table->string('l_name',100)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('email',100)->nullable();
            $table->string('image',100)->nullable();
            $table->foreignId('admin_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('vendor_id')->nullable();
            $table->foreignId('deliveryman_id')->nullable();
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
        Schema::dropIfExists('user_infos');
    }
}
