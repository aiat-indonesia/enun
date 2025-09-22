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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->index();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->json('languages')->nullable(); // Array of languages
            $table->text('summary')->nullable();
            $table->string('type')->nullable(); // manuscript, tafsir, book, journal, etc.
            $table->string('status')->default('draft'); // draft, review, published
            $table->foreignId('primary_place_id')->nullable()->constrained('places')->onDelete('set null');
            $table->json('metadata')->nullable(); // Flexible metadata
            $table->json('alternative_titles')->nullable(); // Array of alternative titles
            $table->json('external_identifiers')->nullable(); // DOI, ISBN, etc.
            $table->json('seller_links')->nullable(); // Marketplace links
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
