<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseToStockInRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_in_records', function (Blueprint $table) {
            $table->after('id', function (Blueprint $table) {
                $table->bigInteger('purchase_id')->unsigned();

                $table->foreign('purchase_id')
                    ->on('purchase_orders')
                    ->references('id')
                    ->onDelete('cascade');
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
        Schema::table('stock_in_records', function (Blueprint $table) {
            $table->dropForeign('stock_in_records_purchase_id_foreign');
            $table->dropColumn('purchase_id');
        });
    }
}
