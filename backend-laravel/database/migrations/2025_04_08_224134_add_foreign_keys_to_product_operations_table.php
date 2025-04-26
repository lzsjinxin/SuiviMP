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
        Schema::table('product_operations', function (Blueprint $table) {
            $table->foreign(['id_product'], 'product_operations_product_fk')->references(['id'])->on('product')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_operations'], 'product_operations_operations_fk')->references(['id_operation'])->on('operation_definitions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_operations', function (Blueprint $table) {
            $table->dropForeign('product_operations_product_fk');
            $table->dropForeign('product_operations_operations_fk');
        });
    }
};
