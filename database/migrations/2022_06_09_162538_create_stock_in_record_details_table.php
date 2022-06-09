<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockInRecordDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_in_record_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('record_id')->unsigned();
            $table->bigInteger('electric_id')->unsigned();
            $table->decimal('original_price');
            $table->integer('count')->unsigned();
            $table->timestamps();

            // index
            $table->foreign('record_id')
                ->references('id')
                ->on('stock_in_records')
                ->onDelete('cascade');
            $table->foreign('electric_id')
                ->references('id')
                ->on('eletronics')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_in_record_details');
    }
}
