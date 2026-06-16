<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->json('photos')->nullable()->after('catatan');
        });

        DB::statement("UPDATE daily_reports SET photos = '[]'::json WHERE photos IS NULL");

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['progress_persen', 'rencana_besok']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->decimal('progress_persen', 5, 2)->default(0)->after('kendala');
            $table->text('rencana_besok')->nullable()->after('progress_persen');
        });

        DB::statement("UPDATE daily_reports SET rencana_besok = '' WHERE rencana_besok IS NULL");

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn('photos');
        });
    }
};
