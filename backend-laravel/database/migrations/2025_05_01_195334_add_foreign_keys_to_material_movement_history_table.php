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
        Schema::table('material_movement_history', function (Blueprint $table) {
            $table->foreign(['id_location_from'], 'material_movement_history_location_fk')->references(['id'])->on('location');
            $table->foreign(['id_location_to'], 'material_movement_history_location_fk_1')->references(['id'])->on('location');
            $table->foreign(['id_material'], 'material_movement_history_material_fk')->references(['id'])->on('material');
            $table->foreign(['id_movement_type'], 'material_movement_history_movement_type_fk')->references(['id'])->on('movement_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_movement_history', function (Blueprint $table) {
            $table->dropForeign('material_movement_history_location_fk');
            $table->dropForeign('material_movement_history_location_fk_1');
            $table->dropForeign('material_movement_history_material_fk');
            $table->dropForeign('material_movement_history_movement_type_fk');
        });
    }
};
