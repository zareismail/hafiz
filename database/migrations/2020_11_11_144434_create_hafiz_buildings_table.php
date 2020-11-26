<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHafizBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hafiz_buildings', function (Blueprint $table) {
            $table->id();
            $table->slugging('name');
            $table->integer('number')->nullable();
            $table->text('description')->nullable(); 
            $table->foreignId('complex_id')->constrained('hafiz_complexes');
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
        Schema::dropIfExists('hafiz_buildings');
    }
}
