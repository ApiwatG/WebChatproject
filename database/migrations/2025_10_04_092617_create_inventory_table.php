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
        Schema::create('user_cosmetics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cosmetic_id')->constrained('cosmetics')->onDelete('cascade');
            $table->boolean('is_equipped')->default(false);
            $table->timestamp('acquired_at')->useCurrent();
            $table->timestamps();
            
            // User can only own one instance of each cosmetic
            $table->unique(['user_id', 'cosmetic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cosmetics');
    }
};