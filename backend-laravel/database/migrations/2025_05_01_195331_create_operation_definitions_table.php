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
        Schema::create('operation_definitions', function (Blueprint $table) {
            $table->comment('sotres the definitions of all the operations whith their expected time and their needed materials and qty');
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('time_expected')->nullable();
            $table->integer('useradd')->nullable();
            $table->integer('userupdate')->nullable();
            $table->timestamps();
            $table->boolean('active')->nullable();
            $table->decimal('qty_needed', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_definitions');
    }
};
