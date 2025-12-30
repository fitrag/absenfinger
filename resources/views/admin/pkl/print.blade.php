<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sertifikat PKL</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header img {
            height: 80px;
            float: left;
        }

        .header-text {
            margin-left: 90px;
        }

        .header-text h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header-text h3 {
            margin: 5px 0;
            font-size: 14px;
        }

        .header-text p {
            margin: 0;
            font-size: 11px;
        }

        .title {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .group-header {
            background-color: #e6e6e6;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .footer p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    @if($setting && $setting->kop_image)
        <div class="header">
            <!-- Ensure base64 or absolute path for local images if needed, but dompdf handles http usually. 
                                                 Using public_path if file exists locally is safer. -->
            <img src="{{ public_path('storage/' . $setting->kop_image) }}" alt="Logo">
            <div class="header-text">
                <h2>{{ $setting->school_name ?? 'SEKOLAH MENENGAH KEJURUAN' }}</h2>
                <h3>DAFTAR PESERTA PRAKTIK KERJA LAPANGAN (PKL)</h3>
                <p>{{ $setting->school_address ?? 'Alamat Sekolah' }}</p>
            </div>
            <div style="clear: both;"></div>
        </div>
    @else
        <div class="header">
            <h2>DATA PRAKTIK KERJA LAPANGAN (PKL)</h2>
            <h2>TAHUN PELAJARAN {{ $tp->nm_tp ?? '-' }}</h2>
        </div>
    @endif



    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 50%;">Siswa</th>
                <th style="width: 15%;">Kelas</th>
                <th style="width: 30%;">Tempat PKL (DUDI)</th>
            </tr>
        </thead>
        <tbody>
            @php $currentDudiId = null; @endphp
            @foreach($pkls as $index => $pkl)
                <!-- Grouping Logic -->
                @if($currentDudiId !== $pkl->dudi_id)
                    @php $currentDudiId = $pkl->dudi_id; @endphp
                    <tr class="group-header">
                        <td colspan="4">
                            @if($pkl->pembimbingSekolah)
                                <div style="float: right; font-weight: normal; font-size: 11px;">
                                    Pembimbing Sekolah: {{ $pkl->pembimbingSekolah->nama }}
                                </div>
                            @endif
                            <div style="font-size: 13px;">
                                {{ $pkl->dudi->nama }}
                                @if($pkl->pimpinan || $pkl->pembimbing_industri)
                                    <span
                                        style="font-weight: normal;">({{ $pkl->pimpinan ? 'Pimpinan: ' . $pkl->pimpinan : '' }}{{ $pkl->pimpinan && $pkl->pembimbing_industri ? ', ' : '' }}{{ $pkl->pembimbing_industri ? 'Pembimbing Industri: ' . $pkl->pembimbing_industri : '' }})</span>
                                @endif
                            </div>
                            <div style="font-weight: normal; font-size: 10px; margin-top: 2px;">
                                {{ $pkl->dudi->alamat }}
                            </div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $pkl->student->name }} ({{ $pkl->student->nis }})</td>
                    <td style="text-align: center;">{{ $pkl->student->kelas->nm_kls ?? '-' }}</td>
                    <td><!-- Intentionally Empty (covered by group) or Repeats if needed. Using Group Header style requested. -->
                        {{ $pkl->dudi->nama }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>

</html>