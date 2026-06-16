<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi {{ $project->nama_proyek }}</title>
    @php
        $isCompact = $attendanceDates->count() > 20;
        $isVeryCompact = $attendanceDates->count() > 28;
        $dateFontSize = $isVeryCompact ? '5.5px' : ($isCompact ? '6px' : '7px');
        $dateCellPadding = $isCompact ? '2px 1px' : '3px 2px';
        $statusFontSize = $isVeryCompact ? '6px' : ($isCompact ? '7px' : '8px');
        $statusPadding = $isCompact ? '2px 2px' : '3px 4px';
        $dayInitials = [1 => 'S', 2 => 'S', 3 => 'R', 4 => 'K', 5 => 'J', 6 => 'S', 7 => 'M'];
        $monthGroups = $attendanceDates
            ->groupBy(fn (array $date) => \Illuminate\Support\Carbon::parse($date['key'])->format('Y-m'))
            ->map(function ($dates, $monthKey) {
                $monthDate = \Illuminate\Support\Carbon::createFromFormat('Y-m', $monthKey);

                return [
                    'label' => strtoupper($monthDate->translatedFormat('M Y')),
                    'count' => $dates->count(),
                ];
            })
            ->values();
    @endphp
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            margin: {{ $isCompact ? '10mm' : '12mm' }};
        }

        body {
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: {{ $isCompact ? '8px' : '10px' }};
        }

        h1 {
            font-size: {{ $isCompact ? '16px' : '20px' }};
            margin: 0 0 4px;
        }

        .muted {
            color: #64748b;
        }

        .header {
            border-bottom: 1px solid #cbd5e1;
            margin-bottom: {{ $isCompact ? '8px' : '14px' }};
            padding-bottom: {{ $isCompact ? '8px' : '12px' }};
        }

        .meta {
            margin-top: 8px;
            width: 100%;
        }

        .meta td {
            padding: {{ $isCompact ? '2px 0' : '3px 0' }};
            vertical-align: top;
        }

        .legend {
            margin: 0 0 {{ $isCompact ? '6px' : '10px' }};
        }

        .legend span {
            border-radius: 10px;
            display: inline-block;
            margin-right: {{ $isCompact ? '5px' : '8px' }};
            padding: {{ $isCompact ? '2px 5px' : '4px 8px' }};
        }

        table.matrix {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        .matrix th,
        .matrix td {
            border: 1px solid #cbd5e1;
            padding: {{ $dateCellPadding }};
            text-align: center;
            vertical-align: middle;
        }

        .matrix th {
            background: #f1f5f9;
            color: #475569;
            font-size: {{ $dateFontSize }};
            font-weight: bold;
            text-transform: uppercase;
        }

        .matrix .month-head {
            font-size: {{ $isVeryCompact ? '4.8px' : ($isCompact ? '5.2px' : '6.5px') }};
            letter-spacing: .5px;
        }

        .matrix .date-head {
            font-size: {{ $isVeryCompact ? '5.2px' : ($isCompact ? '6px' : '7px') }};
        }

        .matrix .number {
        }

        .matrix .worker {
            background: #f8fafc;
            text-align: left;
            padding-left: {{ $isCompact ? '8px' : '10px' }};
            padding-right: {{ $isCompact ? '8px' : '10px' }};
        }

        .matrix .total-hk {
            background: #0f172a;
            color: #ffffff;
            font-size: {{ $isCompact ? '9px' : '11px' }};
            font-weight: bold;
        }

        .worker-name {
            color: #0f172a;
            font-size: {{ $isCompact ? '9.5px' : '11px' }};
            font-weight: bold;
            line-height: 1.2;
            word-wrap: break-word;
        }

        .job-title {
            color: #64748b;
            font-size: {{ $isCompact ? '7px' : '8px' }};
            margin-top: 2px;
        }

        .status {
            border-radius: {{ $isCompact ? '5px' : '8px' }};
            display: inline-block;
            font-size: {{ $statusFontSize }};
            font-weight: bold;
            min-width: {{ $isCompact ? '10px' : '20px' }};
            padding: {{ $statusPadding }};
        }

        .hadir {
            background: #dcfce7;
            color: #166534;
        }

        .setengah_hari {
            background: #fef3c7;
            color: #92400e;
        }

        .tidak_hadir {
            background: #ffe4e6;
            color: #9f1239;
        }

        .empty {
            background: #f8fafc;
            color: #94a3b8;
        }

        .footer {
            color: #64748b;
            font-size: {{ $isCompact ? '6.5px' : '8px' }};
            margin-top: 12px;
        }

        .date-day {
            color: #64748b;
            font-size: {{ $isVeryCompact ? '4.2px' : ($isCompact ? '4.6px' : '6px') }};
            font-weight: normal;
        }

        .matrix .date-col {
        }

        .table-wrap {
            width: 100%;
            overflow-x: hidden;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Absensi Tenaga Kerja</h1>
        <div class="muted">{{ $project->nama_proyek }} - {{ $project->kode_proyek }}</div>
        <table class="meta">
            <tr>
                <td width="90">Periode</td>
                <td>: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}</td>
                <td width="80">Lokasi</td>
                <td>: {{ $project->lokasi }}</td>
            </tr>
            <tr>
                <td>Klien</td>
                <td>: {{ $project->klien }}</td>
                <td>PIC</td>
                <td>: {{ $project->picNames() ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="legend">
        <span class="hadir">H = Hadir</span>
        <span class="setengah_hari">1/2 = Setengah hari</span>
        <span class="tidak_hadir">TH = Tidak hadir</span>
        <span class="empty">- = Belum ada laporan</span>
    </div>

    <div class="table-wrap">
        <table class="matrix">
            <colgroup>
                <col style="width: 3%;">
                <col style="width: 19%;">
                <col style="width: 5%;">
                @foreach ($attendanceDates as $date)
                    <col>
                @endforeach
            </colgroup>
            <thead>
                <tr>
                    <th class="number" rowspan="3">No</th>
                    <th class="worker" rowspan="3">Pekerja</th>
                    <th class="total-hk" rowspan="3">HK</th>
                    @foreach ($monthGroups as $group)
                        <th class="month-head" colspan="{{ $group['count'] }}">{{ $group['label'] }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($attendanceDates as $date)
                        <th class="date-head">{{ \Illuminate\Support\Carbon::parse($date['key'])->format('d') }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($attendanceDates as $date)
                        <th><span class="date-day">{{ $dayInitials[\Illuminate\Support\Carbon::parse($date['key'])->dayOfWeekIso] }}</span></th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($attendanceRows as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="worker">
                            <div class="worker-name">{{ $row['worker_name'] }}</div>
                            @if ($row['job_title'])
                                <div class="job-title">{{ $row['job_title'] }}</div>
                            @endif
                        </td>
                        <td class="total-hk">{{ number_format($row['total_hk'], fmod($row['total_hk'], 1.0) === 0.0 ? 0 : 1) }}</td>
                        @foreach ($attendanceDates as $date)
                            @php($status = $row['dates'][$date['key']] ?? null)
                            @php($statusShort = match ($status) {
                                'hadir' => 'H',
                                'setengah_hari' => '1/2',
                                'tidak_hadir' => 'TH',
                                default => '-',
                            })
                            <td>
                                <span class="status {{ $status ?: 'empty' }}">{{ $statusShort }}</span>
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 3 + $attendanceDates->count() }}">Belum ada data tenaga kerja pada project ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dicetak {{ $generatedAt->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
