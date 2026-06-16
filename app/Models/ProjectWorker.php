<?php

namespace App\Models;

use Database\Factories\ProjectWorkerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectWorker extends Model
{
    /** @use HasFactory<ProjectWorkerFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'job_title',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(DailyReportWorkerAttendance::class);
    }
}
