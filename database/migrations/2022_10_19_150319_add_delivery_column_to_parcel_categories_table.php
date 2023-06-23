<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryColumnToParcelCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcel_categories', function (Blueprint $table) {
            $table->double('parcel_per_km_shipping_charge', 23, 2)->nullable();
            $table->double('parcel_minimum_shipping_charge', 23, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcel_categories', function (Blueprint $table) {
            $table->dropColumn('parcel_per_km_shipping_charge');
            $table->dropColumn('parcel_minimum_shipping_charge');
        });
    }
}
