<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')
            ->whereNotNull('type')
            ->whereNotIn('type', ['starter', 'case-study', 'dashboard', 'website', 'service', 'tool', 'saas'])
            ->update(['type' => null]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY type ENUM('starter', 'case-study', 'dashboard', 'website', 'service', 'tool', 'saas') NULL DEFAULT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE projects MODIFY type VARCHAR(120) NULL DEFAULT NULL');
        }
    }
};
