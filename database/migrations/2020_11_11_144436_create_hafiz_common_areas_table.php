<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHafizCommonAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hafiz_common_areas', function (Blueprint $table) {
            $table->id();
            $table->naming();
            $table->integer('floor'); 
            $table->text('explanation')->nullable();
            $table->foreignId('building_id')->constrained('hafiz_buildings'); 
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
        Schema::dropIfExists('hafiz_common_areas');
    }
}
