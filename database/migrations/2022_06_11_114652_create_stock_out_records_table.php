<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockOutRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_out_records', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32);
            $table->bigInteger('shipping_type')->unsigned();
            $table->bigInteger('bill_type')->unsigned();
            $table->bigInteger('stock_out_type')->unsigned();
            $table->bigInteger('sell_channel_type')->unsigned();
            $table->timestamp('order_date_time')->default(date('Y-m-d H:i:s'));
            $table->timestamp('shipping_date_time')->default(date('Y-m-d H:i:s'));
            $table->string('address', '512')->default('')->nullable();
            $table->decimal('real_amount', 19, 6);
            $table->decimal('buyer_amount', 19, 6);
            $table->decimal('delivery_charge', 19, 6);
            $table->decimal('discount_amount', 19, 6);
            $table->text('memo')->default('')->nullable();
            $table->timestamps();

            // foreign
            $table->foreign('shipping_type')
                ->references('id')
                ->on('shipping_types')
                ->onDelete('cascade');
            $table->foreign('bill_type')
                ->references('id')
                ->on('bill_types')
                ->onDelete('cascade');
            $table->foreign('stock_out_type')
                ->references('id')
                ->on('stock_out_types')
                ->onDelete('cascade');
            $table->foreign('sell_channel_type')
                ->references('id')
                ->on('sell_channels')
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
        Schema::dropIfExists('stock_out_records');
    }
}
