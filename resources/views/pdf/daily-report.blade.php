<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Harian {{ $report->project->nama_proyek }}</title>
    <style>
        @page {
            margin: 22px;
        }

        body {
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 5px;
        }

        h2 {
            color: #475569;
            font-size: 10px;
            letter-spacing: 1.5px;
            margin: 0 0 8px;
            text-transform: uppercase;
        }

        .muted {
            color: #64748b;
        }

        .header {
            border-bottom: 1px solid #cbd5e1;
            margin-bottom: 14px;
            padding-bottom: 12px;
        }

        .meta {
            border-collapse: collapse;
            margin-top: 10px;
            width: 100%;
        }

        .meta td {
            padding: 4px 0;
            vertical-align: top;
        }

        .section {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            margin-top: 12px;
            padding: 12px;
        }

        table.data {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        .data th,
        .data td {
            border: 1px solid #cbd5e1;
            padding: 7px 6px;
            vertical-align: top;
        }

        .data th {
            background: #f1f5f9;
            color: #475569;
            font-size: 8px;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        .number {
            font-size: 7px;
            text-align: center;
            width: 16px;
        }

        .data th.number,
        .data td.number {
            padding-left: 2px;
            padding-right: 2px;
        }

        .status {
            font-weight: bold;
            width: 90px;
        }

        .photo {
            display: inline-block;
            margin: 0 8px 8px 0;
            vertical-align: top;
        }

        .photo img {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            height: 130px;
            object-fit: cover;
            width: 180px;
        }

        .footer {
            color: #64748b;
            font-size: 8px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Harian Proyek</h1>
        <div class="muted">{{ $report->project->nama_proyek }} - {{ $report->project->kode_proyek }}</div>
        <table class="meta">
            <tr>
                <td width="95">Tanggal</td>
                <td>: {{ $report->tanggal->translatedFormat('d F Y') }}</td>
                <td width="95">Mandor/Pelapor</td>
                <td>: {{ $report->mandor_pelapor }}</td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td>: {{ $report->project->lokasi }}</td>
                <td>Cuaca</td>
                <td>: {{ $report->cuaca }}</td>
            </tr>
            <tr>
                <td>Klien</td>
                <td>: {{ $report->project->klien }}</td>
                <td>Jam Kerja</td>
                <td>: {{ $report->start_time && $report->end_time ? \Carbon\Carbon::parse($report->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($report->end_time)->format('H:i') : '-' }}</td>
            </tr>
            <tr>
                <td>PIC</td>
                <td colspan="3">: {{ $report->project->picNames() ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Uraian Pekerjaan</h2>
        <table class="data">
            <colgroup>
                <col style="width: 16px;">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th class="number">No</th>
                    <th>Uraian pekerjaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report->lineItems('uraian_pekerjaan') as $index => $line)
                    <tr>
                        <td class="number">{{ $index + 1 }}</td>
                        <td>{{ $line }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Absensi Tenaga Kerja</h2>
        <table class="data">
            <colgroup>
                <col style="width: 16px;">
                <col>
                <col style="width: 90px;">
            </colgroup>
            <thead>
                <tr>
                    <th class="number">No</th>
                    <th>Nama pekerja</th>
                    <th class="status">Absen</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report->workerAttendances as $index => $attendance)
                    <tr>
                        <td class="number">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $attendance->worker_name }}</strong>
                            @if ($attendance->job_title)
                                <div class="muted">{{ $attendance->job_title }}</div>
                            @endif
                        </td>
                        <td class="status">{{ $attendance->statusLabel() }}</td>
                    </tr>
                @empty
                    @foreach ($report->lineItems('tenaga_kerja') as $index => $line)
                        <tr>
                            <td class="number">{{ $index + 1 }}</td>
                            <td>{{ $line }}</td>
                            <td class="status">-</td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Material</h2>
        <table class="data">
            <colgroup>
                <col style="width: 16px;">
                <col>
                <col style="width: 90px;">
            </colgroup>
            <thead>
                <tr>
                    <th class="number">No</th>
                    <th>Material</th>
                    <th class="status">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report->materialItems() as $index => $item)
                    <tr>
                        <td class="number">{{ $index + 1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="status">{{ $item['qty'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Daftar Alat Kerja</h2>
        <table class="data">
            <colgroup>
                <col style="width: 16px;">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th class="number">No</th>
                    <th>Alat kerja</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report->lineItems('kendala') as $index => $line)
                    <tr>
                        <td class="number">{{ $index + 1 }}</td>
                        <td>{{ $line }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Catatan</h2>
        <div>{{ $report->catatan ?: '-' }}</div>
    </div>

    @if ($report->photoUrls() !== [])
        <div class="section">
            <h2>Foto Laporan</h2>
            @foreach ($report->photoUrls() as $photo)
                @php($path = public_path('storage/'.$photo))
                @if (file_exists($path))
                    <div class="photo">
                        <img src="{{ $path }}" alt="Foto laporan">
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <div class="footer">
        Dicetak {{ $generatedAt->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
