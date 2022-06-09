<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectronicStorageAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electronic_storage_areas', function (Blueprint $table) {
            $table->bigInteger('electronic_id')->unsigned();
            $table->bigInteger('storage_id')->unsigned();
            $table->timestamps();

            // index
            $table
                ->foreign('electronic_id')
                ->references('id')
                ->on('eletronics')
                ->onDelete('cascade');
            $table
                ->foreign('storage_id')
                ->references('id')
                ->on('storage_areas')
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
        Schema::dropIfExists('electronic_storage_areas');
    }
}
