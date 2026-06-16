<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'kode_proyek',
        'nama_proyek',
        'lokasi',
        'pic',
        'klien',
        'status_aktif',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
        ];
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }

    public function workers(): HasMany
    {
        return $this->hasMany(ProjectWorker::class);
    }

    public function pics(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function picNames(): string
    {
        return $this->pics
            ->pluck('name')
            ->filter()
            ->join(', ');
    }
}
