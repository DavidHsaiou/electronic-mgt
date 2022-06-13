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
            $table->string('essential_name', 128)->default('')->nullable();
            $table->string('image_path', 256)->default('')->nullable();
            $table->integer('pricing')->nullable();
            $table->text('tax_rule')->default('')->nullable();
            $table->string('bill_name', 128)->default('')->nullable();
            $table->text('memo')->default('')->nullable();
            $table->string('options', 256)->default('')->nullable();
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
