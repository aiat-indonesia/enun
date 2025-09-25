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
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')->constrained()->onDelete('cascade');
            $table->string('label'); // Display label for this instance
            $table->foreignIdFor(Agent::class, 'publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Place::class, 'publication_place_id')->nullable()->constrained()->nullOnDelete();
            $table->year('publication_year')->nullable();
            $table->string('format')->nullable(); // print, digital, manuscript, etc.
            $table->json('identifiers')->nullable(); // ISBN, DOI, ISSN, etc.
            $table->json('metadata')->nullable(); // Flexible metadata
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};
