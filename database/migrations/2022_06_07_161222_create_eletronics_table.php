<?php

use App\Models\StorageArea;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEletronicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eletronics', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->integer('count')->default(0);
            $table->string('description', 512);
            $table->string('tags', 256);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eletronics');
    }
}
