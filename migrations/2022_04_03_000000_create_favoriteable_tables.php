<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFavoriteableTables extends Migration
{
    public function up()
    {
        Schema::create('favoriteable_favorites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('favoriteable_id', 36);
            $table->string('favoriteable_type', 255);
            $table->string('user_id', 36)->index();
            $table->timestamps();
            $table->unique(['favoriteable_id', 'favoriteable_type', 'user_id'], 'favoriteable_favorites_unique');
        });
        
        Schema::create('favoriteable_favorite_counters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('favoriteable_id', 36);
            $table->string('favoriteable_type', 255);
            $table->unsignedBigInteger('count')->default(0);
            $table->unique(['favoriteable_id', 'favoriteable_type'], 'favoriteable_counts');
        });
    }

    public function down()
    {
        Schema::drop('favoriteable_favorites');
        Schema::drop('favoriteable_favorite_counters');
    }
}
