<?php

namespace App\Models;

use Database\Factories\DailyReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    /** @use HasFactory<DailyReportFactory> */
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'start_time',
        'end_time',
        'project_id',
        'mandor_pelapor',
        'cuaca',
        'tenaga_kerja',
        'uraian_pekerjaan',
        'material',
        'kendala',
        'catatan',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'photos' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function workerAttendances(): HasMany
    {
        return $this->hasMany(DailyReportWorkerAttendance::class)->orderBy('worker_name');
    }

    public function lineItems(string $attribute): array
    {
        $value = (string) ($this->{$attribute} ?? '');

        return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }

    public function materialItems(): array
    {
        return collect($this->lineItems('material'))
            ->map(function (string $line) {
                if (str_contains($line, ' - ')) {
                    [$name, $qty] = explode(' - ', $line, 2);

                    return ['name' => trim($name), 'qty' => trim($qty)];
                }

                return ['name' => $line, 'qty' => '-'];
            })
            ->all();
    }

    public function photoUrls(): array
    {
        return collect($this->photos ?? [])
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->values()
            ->all();
    }
}
