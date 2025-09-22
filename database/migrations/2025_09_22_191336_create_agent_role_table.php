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
        Schema::create('agent_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->string('agentable_type'); // Polymorphic relationship
            $table->unsignedBigInteger('agentable_id');
            $table->string('role'); // author, editor, translator, publisher, etc.
            $table->timestamps();

            $table->index(['agentable_type', 'agentable_id']);
            $table->unique(['agent_id', 'agentable_type', 'agentable_id', 'role'], 'agent_role_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_role');
    }
};
