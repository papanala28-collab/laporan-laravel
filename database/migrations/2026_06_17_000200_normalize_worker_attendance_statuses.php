<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('daily_report_worker_attendances')
            ->whereIn('status', ['izin', 'sakit', 'alpa'])
            ->update(['status' => 'tidak_hadir']);
    }

    public function down(): void
    {
        DB::table('daily_report_worker_attendances')
            ->where('status', 'tidak_hadir')
            ->update(['status' => 'alpa']);
    }
};
