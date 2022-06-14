<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectronicTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electronic_types', function (Blueprint $table) {
            $table->id();
            $table->string('TypeName', 32);
            $table->tinyInteger('status');
            $table->smallInteger('sort')->unsigned();
            $table->timestamps();
        });

        Schema::table('eletronics', function (Blueprint $table) {
            $table->after('id', function (Blueprint $table) {
                $table->bigInteger('electronic_type')->unsigned();
            });

            $table->foreign('electronic_type')
                ->on('electronic_types')
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
        Schema::table('eletronics', function (Blueprint $table) {
            $table->dropForeign('eletronics_electronic_type_foreign');
            $table->dropColumn('electronic_type');
        });
            Schema::dropIfExists('electronic_types');
    }
}
