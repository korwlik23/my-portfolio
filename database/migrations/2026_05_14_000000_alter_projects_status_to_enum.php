<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')
            ->whereNull('status')
            ->orWhereNotIn('status', ['concept', 'live', 'available', 'demo', 'draft', 'in-progress', 'progress', 'published', 'architecture', 'case-study', 'archived'])
            ->update(['status' => 'concept']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY status ENUM('concept', 'live', 'available', 'demo', 'draft', 'in-progress', 'progress', 'published', 'architecture', 'case-study', 'archived') NOT NULL DEFAULT 'concept'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY status VARCHAR(255) NOT NULL DEFAULT 'concept'");
        }
    }
};
