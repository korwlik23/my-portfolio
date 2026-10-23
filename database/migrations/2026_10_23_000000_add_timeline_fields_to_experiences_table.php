<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('key')->nullable()->unique()->after('id');
            $table->string('kind', 20)->default('work')->index()->after('key');
            $table->date('started_on')->nullable()->after('period');
            $table->date('ended_on')->nullable()->after('started_on');
        });

        DB::table('experiences')
            ->where('period', 'Portfolio highlight')
            ->update(['kind' => 'highlight']);
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->dropIndex(['kind']);
            $table->dropColumn(['key', 'kind', 'started_on', 'ended_on']);
        });
    }
};
