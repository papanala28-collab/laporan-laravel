<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;
use Illuminate\Http\Request;

class ReportPdfController extends Controller
{
    public function __invoke(Request $request, DailyReport $report)
    {
        $report->load('project.pics', 'workerAttendances');

        abort_unless($request->user()?->hasRole('admin') || $report->project->pics->contains($request->user()?->id), 403);

        $fileName = 'laporan-harian-'.str($report->project->kode_proyek)->slug().'-'.$report->tanggal->format('Y-m-d').'.pdf';

        $pdf = Pdf::loadView('pdf.daily-report', [
            'report' => $report,
            'generatedAt' => now(),
        ])
            ->setPaper('a4');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename='.$fileName,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
