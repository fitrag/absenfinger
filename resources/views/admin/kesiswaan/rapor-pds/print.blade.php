<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor PDS - {{ $kelasInfo->nm_kls ?? 'Kelas' }} - {{ $semester }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            padding: 15px 50px 15px 100px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
        }

        .info-box {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .info-box table {
            border-collapse: collapse;
        }

        .info-box td {
            padding: 3px 15px 3px 0;
        }

        .info-box td:first-child {
            font-weight: bold;
            width: 150px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            padding: 5px 10px;
            background-color: #f0f0f0;
            border-left: 4px solid #333;
        }

        .section-title.keterlambatan {
            border-left-color: #f59e0b;
            color: #b45309;
        }

        .section-title.pelanggaran {
            border-left-color: #ef4444;
            color: #dc2626;
        }

        .section-title.konseling {
            border-left-color: #06b6d4;
            color: #0891b2;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #000;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            word-wrap: break-word;
        }

        table.data th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        table.data td.center {
            text-align: center;
        }

        .status-warning {
            color: #d97706;
            font-weight: bold;
        }

        .status-danger {
            color: #dc2626;
            font-weight: bold;
        }

        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
        }

        .signature-area {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .signature {
            text-align: center;
            width: 200px;
        }

        .signature p {
            margin-bottom: 70px;
        }

        @media print {
            @page {
                margin: 5mm 10mm 10mm 10mm;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none;
            }

            .page-break {
                page-break-before: always;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background-color: #2563eb;
        }

        .summary-table {
            margin-bottom: 20px;
        }

        .summary-table td {
            padding: 5px 15px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #b45309;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .badge-info {
            background-color: #cffafe;
            color: #0891b2;
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Cetak</button>

    <div class="header">
        <h1>{{ $settings['school_name'] ?? 'SMK NEGERI 1 SEPUTIH AGUNG' }}</h1>
        <h2>RAPOR PDS KESISWAAN</h2>
        <p>Tahun Pelajaran {{ $tpInfo->nm_tp ?? '-' }} - Semester {{ $semester }}</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>Kelas</td>
                <td>: {{ $kelasInfo->nm_kls ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tahun Pelajaran</td>
                <td>: {{ $tpInfo->nm_tp ?? '-' }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>: {{ $semester }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary Box -->
    <div class="info-box">
        <table class="summary-table">
            <tr>
                <td><strong>Ringkasan Data:</strong></td>
                <td></td>
            </tr>
            <tr>
                <td>• Keterlambatan</td>
                <td>: {{ $keterlambatan->count() }} siswa ({{ $keterlambatan->sum('total_terlambat') }}x terlambat,
                    {{ $keterlambatan->sum('total_menit') }} menit)</td>
            </tr>
            <tr>
                <td>• Pelanggaran</td>
                <td>: {{ $pelanggaran->count() }} siswa ({{ $pelanggaran->sum('total_pelanggaran') }}x pelanggaran,
                    {{ $pelanggaran->sum('total_poin') }} poin)</td>
            </tr>
            <tr>
                <td>• Konseling</td>
                <td>: {{ $konseling->count() }} siswa ({{ $konseling->sum('total_konseling') }}x sesi)</td>
            </tr>
        </table>
    </div>

    <!-- KETERLAMBATAN SECTION -->
    <div class="section-title keterlambatan">📋 DATA KETERLAMBATAN</div>
    @if($keterlambatan->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 60px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 80px;">Jumlah Terlambat</th>
                    <th style="width: 80px;">Total Menit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keterlambatan as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $item->student->nis }}</td>
                        <td>{{ $item->student->name }}</td>
                        <td class="center {{ $item->total_terlambat >= 3 ? 'status-danger' : 'status-warning' }}">
                            {{ $item->total_terlambat }}x
                        </td>
                        <td class="center">{{ $item->total_menit }} menit</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty-message">Tidak ada data keterlambatan</p>
    @endif

    <!-- PELANGGARAN SECTION -->
    <div class="section-title pelanggaran">⚠️ DATA PELANGGARAN</div>
    @if($pelanggaran->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 60px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 100px;">Jumlah Pelanggaran</th>
                    <th style="width: 80px;">Total Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pelanggaran as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $item->student->nis }}</td>
                        <td>{{ $item->student->name }}</td>
                        <td class="center">{{ $item->total_pelanggaran }}x</td>
                        <td class="center {{ $item->total_poin >= 50 ? 'status-danger' : 'status-warning' }}">
                            {{ $item->total_poin }} poin
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty-message">Tidak ada data pelanggaran</p>
    @endif

    <!-- KONSELING SECTION -->
    <div class="section-title konseling">💬 DATA KONSELING</div>
    @if($konseling->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 60px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 100px;">Jumlah Konseling</th>
                </tr>
            </thead>
            <tbody>
                @foreach($konseling as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $item->student->nis }}</td>
                        <td>{{ $item->student->name }}</td>
                        <td class="center">{{ $item->total_konseling }}x</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty-message">Tidak ada data konseling</p>
    @endif

    <!-- Signature -->
    <div class="signature-area">
        <div class="signature">
            <p>{{ $settings['city'] ?? 'Seputih Agung' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Waka Kesiswaan,</p>
            <p><strong>{{ $settings['waka_kesiswaan_name'] ?? '_________________________' }}</strong></p>
            @if(!empty($settings['waka_kesiswaan_nip']))
                <p>NIP. {{ $settings['waka_kesiswaan_nip'] }}</p>
            @endif
        </div>
    </div>
</body>

</html>