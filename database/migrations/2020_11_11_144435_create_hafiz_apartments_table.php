<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHafizApartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hafiz_apartments', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->integer('floor');
            $table->unsignedBigInteger('code', 8)->unique();
            $table->text('description')->nullable(); 
            $table->foreignId('building_id')->constrained('hafiz_buildings');
            $table->auth();
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
        Schema::dropIfExists('hafiz_apartments');
    }
}
