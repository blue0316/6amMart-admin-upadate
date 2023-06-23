<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaximumCodOrderAmountColumnToModuleZoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('module_zone', function (Blueprint $table) {
            $table->double('maximum_cod_order_amount', 23, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('module_zone', function (Blueprint $table) {
            $table->dropColumn('maximum_cod_order_amount');
        });
    }
}
