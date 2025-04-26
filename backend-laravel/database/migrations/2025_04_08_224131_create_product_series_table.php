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
        Schema::create('product_series', function (Blueprint $table) {
            $table->increments('id');
            $table->string('series', 100)->nullable();
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
        Schema::dropIfExists('product_series');
    }
};
