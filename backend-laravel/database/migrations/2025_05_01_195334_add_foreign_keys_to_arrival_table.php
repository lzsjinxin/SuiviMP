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
        Schema::table('arrival', function (Blueprint $table) {
            $table->foreign(['id_tier'], 'arrival_tier_fk')->references(['id'])->on('tier')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('arrival', function (Blueprint $table) {
            $table->dropForeign('arrival_tier_fk');
        });
    }
};
