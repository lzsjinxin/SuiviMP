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
        Schema::create('material', function (Blueprint $table) {
            $table->comment('stores raw materials themselves');
            $table->increments('id');
            $table->integer('id_current_location')->nullable();
            $table->integer('id_status')->nullable();
            $table->integer('id_arrival')->nullable();
            $table->integer('id_type')->nullable();
            $table->integer('id_unit')->nullable();
            $table->integer('num')->nullable();
            $table->integer('useradd')->nullable();
            $table->integer('userupdate')->nullable();
            $table->timestamps();
            $table->boolean('active')->nullable();
            $table->boolean('can_be_expired')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->decimal('qty', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material');
    }
};
