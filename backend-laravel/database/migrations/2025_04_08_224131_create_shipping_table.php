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
        Schema::create('shipping', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('shipping_date')->nullable();
            $table->timestamp('arrival_date')->nullable();
            $table->string('incoterm', 100)->nullable();
            $table->integer('useradd')->nullable();
            $table->integer('userupdate')->nullable();
            $table->timestamps();
            $table->boolean('active')->nullable()->default(true);
            $table->integer('id_product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping');
    }
};
