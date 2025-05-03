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
        Schema::table('material', function (Blueprint $table) {
            $table->foreign(['id_arrival'], 'material_arrival_fk')->references(['id'])->on('arrival')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_status'], 'material_material_status_fk')->references(['id'])->on('material_status')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_type'], 'material_material_type_fk')->references(['id'])->on('material_type')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_current_location'], 'material_stock_fk')->references(['id'])->on('location')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign(['id_unit'], 'material_unit_fk')->references(['id'])->on('unit')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material', function (Blueprint $table) {
            $table->dropForeign('material_arrival_fk');
            $table->dropForeign('material_material_status_fk');
            $table->dropForeign('material_material_type_fk');
            $table->dropForeign('material_stock_fk');
            $table->dropForeign('material_unit_fk');
        });
    }
};
