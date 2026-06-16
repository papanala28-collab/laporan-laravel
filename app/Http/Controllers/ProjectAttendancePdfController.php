<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectAttendanceMatrix;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProjectAttendancePdfController extends Controller
{
    public function __invoke(Request $request, Project $project, ProjectAttendanceMatrix $matrix)
    {
        $project->load('pics');

        abort_unless($request->user()?->hasRole('admin') || $project->pics->contains($request->user()?->id), 403);

        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $validated['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $validated['end_date'] ?? now()->toDateString();
        $attendanceMatrix = $matrix->build($project, $startDate, $endDate);
        $dateCount = $attendanceMatrix['dates']->count();
        $paperSize = 'a4';

        $fileName = 'rekap-absensi-'.str($project->kode_proyek)->slug().'-'.$startDate.'-'.$endDate.'.pdf';

        $pdf = Pdf::loadView('pdf.project-attendance', [
            'project' => $project,
            'startDate' => Carbon::parse($startDate),
            'endDate' => Carbon::parse($endDate),
            'attendanceDates' => $attendanceMatrix['dates'],
            'attendanceRows' => $attendanceMatrix['rows'],
            'attendanceStatuses' => $attendanceMatrix['statuses'],
            'generatedAt' => now(),
            'paperSize' => $paperSize,
        ])
            ->setPaper($paperSize, 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename='.$fileName,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
