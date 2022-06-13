<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEletronicsWorkStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eletronics_work_states', function (Blueprint $table) {
            $table->bigInteger('electronic_id')->unsigned();
            $table->bigInteger('work_state_id')->unsigned();
            $table->timestamps();

            $table->foreign('electronic_id')
                ->references('id')
                ->on('eletronics')
                ->onDelete('cascade');
            $table->foreign('work_state_id')
                ->references('id')
                ->on('work_states')
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
        Schema::dropIfExists('work_states');
    }
}
