<?php

namespace App\Services;

use App\Models\DailyReportWorkerAttendance;
use App\Models\Project;
use App\Models\ProjectWorker;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class ProjectAttendanceMatrix
{
    public function build(Project $project, string $startDate, string $endDate): array
    {
        $attendanceDates = collect(CarbonPeriod::create(
            Carbon::parse($startDate),
            Carbon::parse($endDate)
        ))
            ->map(fn (Carbon $date) => [
                'key' => $date->toDateString(),
                'label' => $date->format('d/m'),
                'day' => $date->translatedFormat('D'),
            ])
            ->values();

        $attendances = DailyReportWorkerAttendance::query()
            ->with('dailyReport')
            ->whereHas('dailyReport', function ($query) use ($project, $startDate, $endDate) {
                $query->where('project_id', $project->id)
                    ->whereDate('tanggal', '>=', $startDate)
                    ->whereDate('tanggal', '<=', $endDate);
            })
            ->orderBy('worker_name')
            ->get();

        $attendanceRows = ProjectWorker::query()
            ->where('project_id', $project->id)
            ->orderBy('name')
            ->get()
            ->map(function (ProjectWorker $worker) use ($attendanceDates, $attendances) {
                $workerAttendances = $attendances
                    ->where('project_worker_id', $worker->id)
                    ->keyBy(fn (DailyReportWorkerAttendance $attendance) => $attendance->dailyReport->tanggal->toDateString());

                return [
                    'worker_name' => $worker->name,
                    'job_title' => $worker->job_title,
                    'dates' => $attendanceDates
                        ->mapWithKeys(fn (array $date) => [$date['key'] => $workerAttendances->get($date['key'])?->status])
                        ->all(),
                    'total_hk' => $this->totalHk($workerAttendances->pluck('status')->all()),
                ];
            });

        $legacyAttendanceRows = $attendances
            ->whereNull('project_worker_id')
            ->groupBy('worker_name')
            ->map(function ($attendances) use ($attendanceDates) {
                $first = $attendances->first();
                $workerAttendances = $attendances
                    ->keyBy(fn (DailyReportWorkerAttendance $attendance) => $attendance->dailyReport->tanggal->toDateString());

                return [
                    'worker_name' => $first->worker_name,
                    'job_title' => $first->job_title,
                    'dates' => $attendanceDates
                        ->mapWithKeys(fn (array $date) => [$date['key'] => $workerAttendances->get($date['key'])?->status])
                        ->all(),
                    'total_hk' => $this->totalHk($workerAttendances->pluck('status')->all()),
                ];
            });

        return [
            'dates' => $attendanceDates,
            'rows' => $attendanceRows->merge($legacyAttendanceRows)->values(),
            'statuses' => DailyReportWorkerAttendance::STATUSES,
        ];
    }

    protected function totalHk(array $statuses): float
    {
        return (float) collect($statuses)->sum(fn (?string $status) => match ($status) {
            'hadir' => 1,
            'setengah_hari' => 0.5,
            default => 0,
        });
    }
}
