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
            // Only add fulltext indexes for MySQL/MariaDB
            $driverName = Schema::connection($this->getConnection())->getConnection()->getDriverName();

            if (in_array($driverName, ['mysql', 'mariadb'])) {
                // Full-text index for search functionality
                $table->fulltext(['title', 'subtitle', 'summary'], 'works_content_fulltext');
            }

            // Regular indexes for all databases including SQLite
            $table->index(['status', 'type'], 'works_status_type_index');
            $table->index(['primary_place_id'], 'works_primary_place_index');
            $table->index(['created_at'], 'works_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            // Drop indexes
            $driverName = Schema::connection($this->getConnection())->getConnection()->getDriverName();

            if (in_array($driverName, ['mysql', 'mariadb'])) {
                $table->dropFulltext('works_content_fulltext');
            }

            $table->dropIndex('works_status_type_index');
            $table->dropIndex('works_primary_place_index');
            $table->dropIndex('works_created_at_index');
        });
    }
};
