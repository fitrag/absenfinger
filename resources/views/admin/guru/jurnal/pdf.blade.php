<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Jurnal Mengajar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            padding: 15px;
        }

        /* KOP Sekolah */
        .kop-sekolah {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .kop-sekolah img {
            max-width: 100%;
            height: auto;
        }

        .page-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-top: 10px;
            text-transform: uppercase;
        }

        /* Info Header */
        .info-header {
            margin-bottom: 15px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .info-cell {
            display: table-cell;
            width: 50%;
        }

        .info-label {
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            width: 130px;
            font-size: 10px;
        }

        .info-value {
            color: #000;
            font-size: 10px;
        }

        /* Data Table */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data th,
        table.data td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
        }

        table.data th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        table.data td.center {
            text-align: center;
        }

        table.data td.number {
            text-align: center;
            width: 25px;
        }

        /* Signature */
        .signature-wrapper {
            margin-top: 30px;
            width: 100%;
        }

        .signature {
            float: right;
            width: 220px;
            text-align: center;
        }

        .signature .date {
            margin-bottom: 5px;
        }

        .signature .title {
            margin-bottom: 5px;
        }

        .signature .name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .signature .nip {
            font-size: 9px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    {{-- KOP Sekolah - Disabled due to GD extension issue
    @if($kopImage)
    @php
    $kopBase64 = '';
    try {
    $kopPath = public_path('storage/' . $kopImage);
    if (file_exists($kopPath)) {
    $imageData = file_get_contents($kopPath);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_buffer($finfo, $imageData);
    finfo_close($finfo);
    $kopBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
    }
    } catch (\Exception $e) {
    $kopBase64 = '';
    }
    @endphp
    @if($kopBase64)
    <div class="kop-sekolah">
        <img src="{{ $kopBase64 }}" alt="Kop Sekolah">
    </div>
    @endif
    @endif
    --}}

    {{-- Info Header --}}
    <div class="page-title">
        JURNAL MENGAJAR GURU
    </div>

    <div class="info-header">
        <div class="info-row">
            <div class="info-cell">
                <span class="info-label">NAMA SEKOLAH</span>
                <span>: </span>
                <span class="info-value">{{ \App\Models\Setting::get('school_name', '-') }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">TAHUN AJARAN</span>
                <span>: </span>
                <span class="info-value">{{ $tp->nm_tp ?? '-' }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell">
                <span class="info-label">MATA PELAJARAN</span>
                <span>: </span>
                <span class="info-value">{{ $mapel->nm_mapel ?? '-' }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">SEMESTER</span>
                <span>: </span>
                <span class="info-value">{{ $semester ?? 'Semua' }}</span>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">Hari/Tanggal</th>
                <th style="width: 40px;">Jam Ke</th>
                <th style="width: 55px;">Kelas</th>
                <th>Materi Pokok/Sub Materi</th>
                <th style="width: 35px;">TM Ke</th>
                <th style="width: 90px;">Selesai/tidak alasan</th>
                <th style="width: 90px;">Absensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jurnals as $index => $jurnal)
                @php
                    $tanggal = \Carbon\Carbon::parse($jurnal->tanggal);
                    $hari = $tanggal->locale('id')->translatedFormat('l');
                @endphp
                <tr>
                    <td class="number">{{ $index + 1 }}</td>
                    <td class="center">{{ $hari }}, {{ $tanggal->format('d/m/Y') }}</td>
                    <td class="center">{{ $jurnal->jam_ke ?? '-' }}</td>
                    <td class="center">{{ $jurnal->kelas->nm_kls ?? '-' }}</td>
                    <td>{{ $jurnal->materi ?? '-' }}</td>
                    <td class="center">{{ $jurnal->tmke ?? '-' }}</td>
                    <td>{{ $jurnal->kegiatan ?? 'selesai' }}</td>
                    <td class="center">{{ $jurnal->absensi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data jurnal</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Signature --}}
    <div class="signature-wrapper clearfix">
        <div class="signature">
            <div class="date">Seputih Agung, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</div>
            <p class="title">Guru Mata Pelajaran</p>
            <div style="height: 60px;"></div>
            <p class="name">{{ $guru->nama ?? '-' }}</p>
            <p class="nip">NIP. {{ $guru->nip ?? '-' }}</p>
        </div>
    </div>
</body>

</html>