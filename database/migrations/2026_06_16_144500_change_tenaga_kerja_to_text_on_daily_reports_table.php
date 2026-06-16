<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE daily_reports ALTER COLUMN tenaga_kerja TYPE TEXT USING tenaga_kerja::text');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE daily_reports ALTER COLUMN tenaga_kerja TYPE INTEGER USING NULLIF(regexp_replace(tenaga_kerja, \'[^0-9]\', \'\', \'g\'), \'\')::integer');
    }
};
