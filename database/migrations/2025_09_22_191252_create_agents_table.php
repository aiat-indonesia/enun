<?php

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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('person'); // person, organization, publisher, etc.
            $table->foreignIdFor(Place::class, 'birth_place')->nullable()->constrained()->nullOnDelete();
            $table->date('birth_date')->nullable();
            $table->foreignIdFor(Place::class, 'death_place')->nullable()->constrained()->nullOnDelete();
            $table->date('death_date')->nullable();
            $table->text('biography')->nullable();
            $table->json('roles')->nullable();
            $table->json('metadata')->nullable(); // Flexible metadata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
