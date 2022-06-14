<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supply_id')->unsigned();
            $table->timestamp('purchase_time');
            $table->decimal('original_amount', 19, 6);
            $table->decimal('original_delivery_charge', 19, 6);
            $table->decimal('delivery_charge', 19, 6);
            $table->decimal('tariff', 19, 6);
            $table->decimal('miscellaneous_branch', 19, 6);
            $table->decimal('charge', 19, 6);
            $table->tinyInteger('status');
            $table->text('memo')->nullable();
            $table->timestamps();


            $table->foreign('supply_id')
                ->on('supply_management')
                ->references('id')
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
        Schema::dropIfExists('purchase_orders');
    }
}
