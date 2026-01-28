<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggaran - {{ $student->name }}</title>
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
            background-color: #fee2e2;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #000;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            border: 2px solid #dc2626;
            background-color: #fef2f2;
        }

        .summary h3 {
            color: #dc2626;
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
        <h2>LAPORAN PELANGGARAN SISWA</h2>
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
                <td>Total Poin</td>
                <td>: <strong style="color: #dc2626;">{{ $totalPoin }} Poin</strong></td>
            </tr>
        </table>
    </div>

    <h3 style="margin-bottom: 10px;">Riwayat Pelanggaran</h3>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 90px;">Tanggal</th>
                <th>Jenis Pelanggaran</th>
                <th style="width: 60px;">Poin</th>
                <th>Tindakan</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 80px;">TTD Siswa</th>
                <th style="width: 100px;">Yang Menindak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelanggarans as $index => $item)
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
                    <td>
                        {{ $item->jenis_pelanggaran }}
                        @if($item->deskripsi)
                            <br><small style="color: #666;">{{ $item->deskripsi }}</small>
                        @endif
                    </td>
                    <td class="center" style="color: #dc2626; font-weight: bold;">{{ $item->poin }}</td>
                    <td>{{ $item->tindakan ?? '-' }}</td>
                    <td class="center {{ $statusClass }}"><strong>{{ $statusLabel }}</strong></td>
                    <td class="center">
                        @if($item->ttd_siswa)
                            <img src="{{ $item->ttd_siswa }}" style="max-height: 40px; max-width: 70px;">
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">{{ $item->creator->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center" style="padding: 20px;">Tidak ada data pelanggaran</td>
                </tr>
            @endforelse
            @if($pelanggarans->count() > 0)
                <tr class="total-row">
                    <td colspan="3" class="right">Total Poin Pelanggaran:</td>
                    <td class="center" style="color: #dc2626;">{{ $totalPoin }}</td>
                    <td colspan="4"></td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($totalPoin >= 50)
        <div class="summary">
            <h3>⚠️ Peringatan</h3>
            <p>Siswa telah mencapai {{ $totalPoin }} poin pelanggaran.</p>
            @if($totalPoin >= 100)
                <p><strong>CATATAN: Siswa telah melampaui batas maksimal poin pelanggaran dan direkomendasikan untuk tindakan
                        disiplin tegas.</strong></p>
            @elseif($totalPoin >= 75)
                <p>Perlu perhatian khusus dan konseling intensif.</p>
            @else
                <p>Diperlukan pembinaan lebih lanjut.</p>
            @endif
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; margin-top: 25px;">
        <div style="text-align: center; width: 30%;">
            <p>Orang Tua/Wali,</p>
            <br><br><br><br>
            <p>_________________________</p>
        </div>
        <div style="text-align: center; width: 30%;">
            <p>Siswa,</p>
            @php
                $latestSignature = $pelanggarans->whereNotNull('ttd_siswa')->first();
            @endphp
            @if($latestSignature && $latestSignature->ttd_siswa)
                <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                    
                </div>
            @else
                <br><br><br><br>
            @endif
            <p><strong>{{ $student->name }}</strong></p>
        </div>
        <div style="text-align: center; width: 30%;">
            <p>{{ $settings['city'] ?? 'Seputih Agung' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Guru BK / Kesiswaan,</p>
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