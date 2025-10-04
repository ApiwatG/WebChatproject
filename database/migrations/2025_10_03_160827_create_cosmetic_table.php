<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cosmetic', function (Blueprint $table) {
             $table->id();
    $table->integer('price');
    $table->string('cosmetic_name');

    $table->timestamps();
    $table->softDeletes();

    $table->foreign('rarity_id')
          ->references('id')
          ->on('rarities')
          ->onDelete('cascade');

    $table->foreign('cosmetic_type_id')
          ->references('id')
          ->on('cosmetic_types')
          ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cosmetic');
    }
};
