<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportWorkerAttendance extends Model
{
    public const STATUSES = [
        'hadir' => 'Hadir',
        'setengah_hari' => 'Setengah hari',
        'tidak_hadir' => 'Tidak hadir',
    ];

    protected $fillable = [
        'daily_report_id',
        'project_worker_id',
        'worker_name',
        'job_title',
        'status',
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function projectWorker(): BelongsTo
    {
        return $this->belongsTo(ProjectWorker::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
