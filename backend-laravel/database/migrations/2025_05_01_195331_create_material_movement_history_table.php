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
        Schema::create('material_movement_history', function (Blueprint $table) {
            $table->comment('stores movement history of raw materials');
            $table->integer('id')->primary();
            $table->integer('id_material')->nullable();
            $table->integer('id_movement_type')->nullable();
            $table->integer('id_location_from')->nullable();
            $table->integer('id_location_to')->nullable();
            $table->timestamp('mouvement_date')->nullable();
            $table->integer('id_user')->nullable();
            $table->boolean('active')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_movement_history');
    }
};
