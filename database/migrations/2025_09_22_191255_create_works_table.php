<?php

use App\Models\Agent;
use App\Models\Place;
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
            $table->string('type')->nullable(); // manuscript, tafsir, book, journal, etc.
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->json('summary')->nullable();
            $table->foreignIdFor(Agent::class, 'author_id')->nullable()->constrained()->nullOnDelete();
            $table->json('contributors')->nullable();
            $table->foreignIdFor(Place::class, 'place_id')->nullable()->constrained()->nullOnDelete();
            $table->json('creation_year')->nullable();
            $table->json('metadata')->nullable(); // Flexible metadata
            $table->string('status')->default('draft'); // draft, in_review, published, archived
            $table->string('visibility')->default('private'); // private, public, restricted
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
