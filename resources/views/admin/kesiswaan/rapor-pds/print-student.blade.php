<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan PDS Siswa - {{ $student->name }} - {{ $semester }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            padding: 20px 50px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
        }

        .student-info {
            margin-bottom: 25px;
            width: 100%;
        }

        .student-info td {
            padding: 4px 10px 4px 0;
            vertical-align: top;
        }

        .student-info td:first-child {
            width: 130px;
            font-weight: bold;
        }

        .student-info td:nth-child(2) {
            width: 20px;
        }

        .section-header {
            background-color: #f0f0f0;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 12px;
            margin: 20px 0 10px 0;
            border-left: 4px solid #333;
            text-transform: uppercase;
        }

        .section-header.terlambat {
            border-left-color: #f59e0b;
            color: #b45309;
        }

        .section-header.pelanggaran {
            border-left-color: #ef4444;
            color: #dc2626;
        }

        .section-header.konseling {
            border-left-color: #06b6d4;
            color: #0891b2;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        table.data th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .empty-data {
            text-align: center;
            padding: 15px;
            border: 1px dashed #ccc;
            color: #666;
            margin-bottom: 20px;
            font-style: italic;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature {
            text-align: center;
            width: 220px;
        }

        .signature p {
            margin-bottom: 5px;
        }

        .signature .name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .print-button:hover {
            background-color: #2563eb;
        }

        @media print {
            @page {
                margin: 10mm;
            }

            body {
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Cetak</button>

    <div class="header">
        <h1>{{ $settings['school_name'] ?? 'SMK NEGERI 1 SEPUTIH AGUNG' }}</h1>
        <h2>LAPORAN KEDISIPLINAN SISWA (PDS)</h2>
        <p>Tahun Pelajaran {{ $tpInfo->nm_tp ?? '-' }} - Semester {{ $semester }}</p>
    </div>

    <table class="student-info">
        <tr>
            <td>Nama Siswa</td>
            <td>:</td>
            <td><strong>{{ $student->name }}</strong></td>
            <td>NIS</td>
            <td>:</td>
            <td>{{ $student->nis }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $student->kelas->nm_kls ?? '-' }}</td>
            <td>Jurusan</td>
            <td>:</td>
            <td>{{ $student->kelas->jurusan ?? '-' }}</td>
        </tr>
    </table>

    <!-- KETERLAMBATAN -->
    <div class="section-header terlambat">I. KETERLAMBATAN SISWA</div>
    @if($keterlambatan->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th style="width: 80px;">Jam Datang</th>
                    <th style="width: 80px;">Terlambat</th>
                    <th>Alasan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keterlambatan as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->jam_datang)->format('H:i') }}</td>
                        <td class="text-center">{{ $item->keterlambatan_menit }} Menit</td>
                        <td>{{ $item->alasan ?? '-' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right"><strong>Total</strong></td>
                    <td class="text-center"><strong>{{ $keterlambatan->sum('keterlambatan_menit') }} Menit</strong></td>
                    <td colspan="2"><strong>{{ $keterlambatan->count() }}x Terlambat</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty-data">Tidak ada catatan keterlambatan pada periode ini.</div>
    @endif

    <!-- PELANGGARAN -->
    <div class="section-header pelanggaran">II. PELANGGARAN TATA TERTIB</div>
    @if($pelanggaran->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Detail / Keterangan</th>
                    <th style="width: 60px;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pelanggaran as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                        <td>{{ $item->jenis_pelanggaran ?? 'Pelanggaran Umum' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="text-center">{{ $item->poin }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-right"><strong>Total Poin Pelanggaran</strong></td>
                    <td class="text-center"><strong>{{ $pelanggaran->sum('poin') }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty-data">Tidak ada catatan pelanggaran pada periode ini.</div>
    @endif

    <!-- KONSELING -->
    <div class="section-header konseling">III. CATATAN KONSELING</div>
    @if($konseling->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th>Permasalahan</th>
                    <th>Hasil Konseling</th>
                    <th style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($konseling as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                        <td>{{ $item->permasalahan ?? '-' }}</td>
                        <td>{{ $item->hasil_konseling ?? '-' }}</td>
                        <td class="text-center">{{ ucfirst($item->status ?? '-') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-data">Tidak ada catatan konseling pada periode ini.</div>
    @endif

    <!-- SIGNATURE -->
    <div class="signature-area">
        <div class="signature">
            <p>Mengetahui,</p>
            <p>Waka Kesiswaan</p>
            <p class="name">{{ $settings['waka_kesiswaan_name'] ?? '________________________' }}</p>
            <p>NIP. {{ $settings['waka_kesiswaan_nip'] ?? '-' }}</p>
        </div>
        <div class="signature">
            <p>{{ $settings['city'] ?? 'Seputih Agung' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Guru BK / Pembimbing</p>
            <p class="name">________________________</p>
            <p>NIP. .......................</p>
        </div>
    </div>

</body>

</html>