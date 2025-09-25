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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instance_id')->constrained()->cascadeOnDelete();
            $table->string('identifier')->nullable(); // Barcode, shelfmark, etc.
            $table->foreignIdFor(Place::class, 'location')->nullable()->constrained()->nullOnDelete();
            $table->string('condition')->nullable(); // good, fair, poor, etc.
            $table->foreignIdFor(Agent::class, 'current_holder')->nullable()->constrained()->nullOnDelete();
            $table->json('metadata')->nullable(); // Flexible metadata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
