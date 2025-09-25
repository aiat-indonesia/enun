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
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('city'); // province, regency, city, village, etc.
            $table->foreignId('parent_id')->nullable()->constrained('places')->onDelete('set null');
            $table->decimal('latitude', 10, 7)->nullable(); // Centroid latitude
            $table->decimal('longitude', 10, 7)->nullable(); // Centroid longitude
            $table->json('metadata')->nullable(); // Flexible metadata
            $table->timestamps();

            $table->index(['latitude', 'longitude']); // For geospatial queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
