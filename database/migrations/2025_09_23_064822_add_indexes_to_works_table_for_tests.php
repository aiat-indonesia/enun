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
        Schema::table('works', function (Blueprint $table) {
            // Only add regular indexes for test environment
            if (app()->environment('testing')) {
                $table->index(['title'], 'works_title_index');
                $table->index(['subtitle'], 'works_subtitle_index');
                $table->index(['summary'], 'works_summary_index');
                $table->index(['slug'], 'works_slug_index');
                $table->index(['status', 'type'], 'works_status_type_test_index');
                $table->index(['primary_place_id'], 'works_primary_place_test_index');
                $table->index(['created_at'], 'works_created_at_test_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            if (app()->environment('testing')) {
                $table->dropIndex('works_title_index');
                $table->dropIndex('works_subtitle_index');
                $table->dropIndex('works_summary_index');
                $table->dropIndex('works_slug_index');
                $table->dropIndex('works_status_type_test_index');
                $table->dropIndex('works_primary_place_test_index');
                $table->dropIndex('works_created_at_test_index');
            }
        });
    }
};
