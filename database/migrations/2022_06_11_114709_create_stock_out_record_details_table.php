<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockOutRecordDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_out_record_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('record_id')->unsigned();
            $table->biginteger('electric_id')->unsigned();
            $table->decimal('single_price', 19, 6);
            $table->integer('count')->unsigned();
            $table->timestamps();

            $table->foreign('record_id')
                ->references('id')
                ->on('stock_out_records')
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
        Schema::dropIfExists('stock_out_record_details');
    }
}
