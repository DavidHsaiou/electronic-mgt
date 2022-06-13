<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedCountToStockIn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_in_record_details', function (Blueprint $table) {
            $table->after('count', function ($table) {
                $table->integer('used_count')->default(0)->unsigned();
                $table->tinyInteger('status')->default(0)->unsigned();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_in_record_details', function (Blueprint $table) {
            $table->dropColumn('used_count');
            $table->dropColumn('status');

        });
    }
}
