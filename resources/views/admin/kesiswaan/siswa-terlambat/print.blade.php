<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keterlambatan - {{ $student->name }}</title>
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
            width: 150px;
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

        table.data td.right {
            text-align: right;
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

        .total-row {
            background-color: #fef3c7;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #000;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            border: 2px solid #f59e0b;
            background-color: #fffbeb;
        }

        .summary h3 {
            color: #d97706;
            margin-bottom: 10px;
        }

        .summary p {
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .footer p {
            margin-bottom: 60px;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature {
            text-align: center;
            width: 45%;
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
        <h2>LAPORAN KETERLAMBATAN SISWA</h2>
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
                <td>Total Keterlambatan</td>
                <td>: <strong style="color: #d97706;">{{ $totalTerlambat }}x ({{ $totalMenit }} menit)</strong></td>
            </tr>
        </table>
    </div>

    <h3 style="margin-bottom: 10px;">Riwayat Keterlambatan</h3>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th style="width: 60px;">Jam Datang</th>
                <th style="width: 60px;">Terlambat</th>
                <th>Alasan</th>
                <th style="width: 70px;">Status</th>
                <th style="width: 100px;">Yang Menindak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lateRecords as $index => $item)
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
                    <td class="center">{{ $item->tanggal->translatedFormat('D, d M Y') }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->jam_datang)->format('H:i') }}</td>
                    <td class="center" style="color: #d97706; font-weight: bold;">{{ $item->keterlambatan_menit }} mnt</td>
                    <td>{{ $item->alasan ?? '-' }}</td>
                    <td class="center {{ $statusClass }}"><strong>{{ $statusLabel }}</strong></td>
                    <td class="center">{{ $item->creator->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center" style="padding: 20px;">Tidak ada data keterlambatan</td>
                </tr>
            @endforelse
            @if($lateRecords->count() > 0)
                <tr class="total-row">
                    <td colspan="3" class="right">Total Keterlambatan:</td>
                    <td class="center" style="color: #d97706;">{{ $totalMenit }} menit</td>
                    <td colspan="3"></td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($totalTerlambat >= 3)
        <div class="summary">
            <h3>⚠️ Peringatan</h3>
            <p>Siswa telah terlambat sebanyak {{ $totalTerlambat }} kali dengan total {{ $totalMenit }} menit.</p>
            @if($totalTerlambat >= 10)
                <p><strong>CATATAN: Siswa telah melampaui batas maksimal keterlambatan dan direkomendasikan untuk tindakan
                        disiplin tegas.</strong></p>
            @elseif($totalTerlambat >= 5)
                <p>Perlu perhatian khusus dan pembinaan dari orang tua/wali.</p>
            @else
                <p>Diperlukan pembinaan lebih lanjut agar tidak terulang.</p>
            @endif
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; margin-top: 25px;">
        <div style="text-align: center; width: 24%;">
            <p>Orang Tua/Wali,</p>
            <br><br><br><br>
            <p>_________________________</p>
        </div>
        <div style="text-align: center; width: 24%;">
            <p>Siswa,</p>
            <br><br><br><br>
            <p><strong>{{ $student->name }}</strong></p>
        </div>
        <div style="text-align: center; width: 24%;">
            <p>Wali Kelas,</p>
            <br><br><br><br>
            @php
                $walasName = $walas && $walas->guru ? $walas->guru->nama : '_________________________';
                $walasNip = $walas && $walas->guru ? $walas->guru->nip : null;
            @endphp
            <p><strong>{{ $walasName }}</strong></p>
            @if($walasNip)
                <p>NIP. {{ $walasNip }}</p>
            @endif
        </div>
        <div style="text-align: center; width: 24%;">
            <p>{{ $settings['city'] ?? 'Seputih Agung' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Waka Kesiswaan,</p>
            <br><br><br><br>
            <p><strong>{{ $settings['waka_kesiswaan_name'] ?? '_________________________' }}</strong></p>
            @if(!empty($settings['waka_kesiswaan_nip']))
                <p>NIP. {{ $settings['waka_kesiswaan_nip'] }}</p>
            @endif
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