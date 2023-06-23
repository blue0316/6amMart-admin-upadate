<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModuleZone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('module_zone')){
            Schema::create('module_zone', function (Blueprint $table) {
                $table->id();
                $table->foreignId('module_id');
                $table->foreignId('zone_id');
                $table->double('per_km_shipping_charge', 23, 2)->nullable();
                $table->double('minimum_shipping_charge', 23, 2)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
