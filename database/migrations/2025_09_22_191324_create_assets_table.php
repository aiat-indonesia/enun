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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('assetable_type'); // Polymorphic relationship
            $table->unsignedBigInteger('assetable_id');
            $table->string('disk')->default('local'); // Storage disk
            $table->string('path'); // File path on disk
            $table->string('filename'); // Original filename
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable(); // File size in bytes
            $table->text('extracted_text')->nullable(); // For text search indexing
            $table->json('metadata')->nullable(); // Flexible metadata
            $table->timestamps();

            $table->index(['assetable_type', 'assetable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
