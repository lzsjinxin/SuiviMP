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
        Schema::table('operations', function (Blueprint $table) {
            $table->foreign(['id_material'], 'operations_material_fk')->references(['id'])->on('material')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_product'], 'operations_product_fk')->references(['id'])->on('product')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_product_operations'], 'operations_product_operations_fk')->references(['id'])->on('product_operations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign('operations_material_fk');
            $table->dropForeign('operations_product_fk');
            $table->dropForeign('operations_product_operations_fk');
        });
    }
};
