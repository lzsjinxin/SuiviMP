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
        Schema::create('operations', function (Blueprint $table) {
            $table->comment('stores the history of the operations that realistically happened to the raw material aswell as for which product');
            $table->increments('id');
            $table->integer('id_product')->nullable();
            $table->integer('id_material')->nullable();
            $table->integer('id_product_operations')->nullable();
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->integer('useradd')->nullable();
            $table->integer('userupdate')->nullable();
            $table->timestamps();
            $table->boolean('active')->nullable()->default(true);
            $table->decimal('qty_used', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
};
