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
        Schema::create('cosmetics', function (Blueprint $table) {
            $table->id();
            $table->string('cosmetic_name');
            $table->integer('price');
            $table->string('cosmetic_img')->nullable();
            $table->foreignId('rarity_id')->constrained('rarity')->onDelete('cascade');
            $table->foreignId('cosmetic_type_id')->constrained('cosmetic_type')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
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
