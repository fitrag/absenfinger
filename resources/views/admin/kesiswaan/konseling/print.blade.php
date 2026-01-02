<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Konseling - {{ $student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }

        .header img.kop {
            max-height: 100px;
            margin-bottom: 10px;
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

        .student-info {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .student-info table {
            border-collapse: collapse;
        }

        .student-info td {
            padding: 3px 15px 3px 0;
        }

        .student-info td:first-child {
            font-weight: bold;
            width: 120px;
        }

        table.data {
            width: 99%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            border: 1px solid #000;
            box-sizing: border-box;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
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

        .status-pending {
            color: #d97706;
        }

        .status-diproses {
            color: #2563eb;
        }

        .status-selesai {
            color: #16a34a;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #2563eb;
            background-color: #eff6ff;
        }

        .summary h3 {
            color: #2563eb;
            margin-bottom: 10px;
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
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Cetak</button>

    <div class="header">
        @if(!empty($settings['kop_image']))
            <img src="{{ asset('storage/' . $settings['kop_image']) }}" class="kop" alt="Kop Surat">
        @else
            <h1>{{ $settings['school_name'] ?? 'SMK NEGERI 1 SEPUTIH AGUNG' }}</h1>
        @endif
        <h2>LAPORAN BIMBINGAN DAN KONSELING SISWA</h2>
        <p>Tahun Pelajaran {{ $settings['school_year'] ?? date('Y') . '/' . (date('Y') + 1) }}</p>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td>Nama Siswa</td>
                <td>: {{ $student->name }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: {{ $student->nis }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{ $student->kelas->nm_kls ?? '-' }}</td>
            </tr>
            <tr>
                <td>Total Sesi</td>
                <td>: <strong>{{ $konselings->count() }} sesi konseling</strong></td>
            </tr>
        </table>
    </div>

    <h3 style="margin-bottom: 10px;">Riwayat Konseling</h3>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 90px;">Tanggal</th>
                <th>Permasalahan</th>
                <th>Penanganan</th>
                <th>Hasil</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($konselings as $index => $item)
                @php
                    $statusClass = match ($item->status) {
                        'pending' => 'status-pending',
                        'diproses' => 'status-diproses',
                        'selesai' => 'status-selesai',
                        default => ''
                    };
                    $statusLabel = match ($item->status) {
                        'pending' => 'Pending',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        default => $item->status
                    };
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->permasalahan }}</td>
                    <td>{{ $item->penanganan ?? '-' }}</td>
                    <td>{{ $item->hasil ?? '-' }}</td>
                    <td class="center {{ $statusClass }}"><strong>{{ $statusLabel }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center" style="padding: 20px;">Tidak ada data konseling</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $pending = $konselings->where('status', 'pending')->count();
        $diproses = $konselings->where('status', 'diproses')->count();
        $selesai = $konselings->where('status', 'selesai')->count();
    @endphp

    <div class="summary">
        <h3>📊 Ringkasan</h3>
        <p>Total Sesi Konseling: <strong>{{ $konselings->count() }}</strong></p>
        <p>• Pending: {{ $pending }} sesi</p>
        <p>• Diproses: {{ $diproses }} sesi</p>
        <p>• Selesai: {{ $selesai }} sesi</p>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 25px;">
        <div style="text-align: center; width: 30%;">
            <p>Orang Tua/Wali,</p>
            <br><br><br><br>
            <p>_________________________</p>
        </div>
        <div style="text-align: center; width: 30%;">
            <p>Siswa,</p>
            <br><br><br><br>
            <p><strong>{{ $student->name }}</strong></p>
        </div>
        <div style="text-align: center; width: 30%;">
            <p>{{ $settings['city'] ?? 'Seputih Agung' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Guru BK,</p>
            <br><br><br><br>
            <p>_________________________</p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <p>Mengetahui,</p>
        <p>Kepala Sekolah</p>
        <br><br><br><br>
        <p><strong>{{ $settings['principal_name'] ?? '_________________________' }}</strong></p>
        <p>NIP. {{ $settings['principal_nip'] ?? '___________________' }}</p>
    </div>
</body>

</html>