<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHafizComplexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hafiz_complexes', function (Blueprint $table) {
            $table->id();
            $table->slugging('name');
            $table->text('description')->nullable(); 
            $table->location('zone');
            $table->config();
            $table->coordinates();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hafiz_complexes');
    }
}
