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
        Schema::create('arrival', function (Blueprint $table) {
            $table->comment('This table stores all raw material arrivals');
            $table->increments('id');
            $table->timestamp('date')->nullable();
            $table->string('vehicule_registration', 100)->nullable();
            $table->integer('useradd')->nullable();
            $table->integer('userupdate')->nullable();
            $table->timestamps();
            $table->boolean('active')->nullable();
            $table->integer('id_tier')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arrival');
    }
};
