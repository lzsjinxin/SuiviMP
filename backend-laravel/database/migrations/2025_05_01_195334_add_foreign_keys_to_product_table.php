<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->foreign(['id_series'], 'product_product_series_fk')->references(['id'])->on('product_series')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_status'], 'product_product_status_fk')->references(['id'])->on('product_status')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropForeign('product_product_series_fk');
            $table->dropForeign('product_product_status_fk');
        });
    }
};
