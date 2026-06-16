<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('mandor_pelapor');
            $table->string('cuaca');
            $table->unsignedInteger('tenaga_kerja');
            $table->text('uraian_pekerjaan');
            $table->text('material');
            $table->text('kendala');
            $table->decimal('progress_persen', 5, 2);
            $table->text('rencana_besok');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
