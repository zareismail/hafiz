<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHafizEnvironmentalReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hafiz_environmental_reports', function (Blueprint $table) {
            $table->id(); 
            $table->auth();
            $table->details();
            $table->integer('value');
            $table->foreignId('environmental_id')->constrained('hafiz_environmentals'); 
            $table->morphs('reportable');  
            $table->timestamp('target_date')->nullable();
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
        Schema::dropIfExists('hafiz_environmental_reports');
    }
}
