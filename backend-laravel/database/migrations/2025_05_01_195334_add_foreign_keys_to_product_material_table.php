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
        Schema::table('product_material', function (Blueprint $table) {
            $table->foreign(['id_material'], 'product_material_material_fk')->references(['id'])->on('material')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_product'], 'product_material_product_fk')->references(['id'])->on('product')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_material', function (Blueprint $table) {
            $table->dropForeign('product_material_material_fk');
            $table->dropForeign('product_material_product_fk');
        });
    }
};
