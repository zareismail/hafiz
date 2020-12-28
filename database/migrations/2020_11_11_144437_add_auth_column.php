<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zareismail\NovaContracts\Models\User;

class AddAuthColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::disableForeignKeyConstraints();

        $developer = User::get()->filter->isDeveloper()->first();

        Schema::hasColumn('hafiz_complexes', 'auth_id') || 
        Schema::table('hafiz_complexes', function (Blueprint $table) use ($developer) {
            $table->auth('auth')->default($developer->id); 
        });

        Schema::hasColumn('hafiz_buildings', 'auth_id') ||
        Schema::table('hafiz_buildings', function (Blueprint $table) use ($developer) {
            $table->auth()->default($developer->id); 
        });

        Schema::hasColumn('hafiz_common_areas', 'auth_id') ||
        Schema::table('hafiz_common_areas', function (Blueprint $table) use ($developer) {
            $table->auth()->default($developer->id); 
        }); 

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        Schema::hasColumn('hafiz_complexes', 'auth_id') && 
        Schema::table('hafiz_complexes', function (Blueprint $table) {
            $table->dropAuth(); 
        });

        Schema::hasColumn('hafiz_buildings', 'auth_id') &&
        Schema::table('hafiz_buildings', function (Blueprint $table) {
            $table->dropAuth(); 
        });

        Schema::hasColumn('hafiz_common_areas', 'auth_id') &&
        Schema::table('hafiz_common_areas', function (Blueprint $table) {
            $table->dropAuth(); 
        });  
    }
}
