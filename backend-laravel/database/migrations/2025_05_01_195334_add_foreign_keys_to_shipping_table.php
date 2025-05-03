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
        Schema::table('shipping', function (Blueprint $table) {
            $table->foreign(['id_product'], 'shipping_product_fk')->references(['id'])->on('product')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_tier'], 'shipping_tier_fk')->references(['id'])->on('tier')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping', function (Blueprint $table) {
            $table->dropForeign('shipping_product_fk');
            $table->dropForeign('shipping_tier_fk');
        });
    }
};
