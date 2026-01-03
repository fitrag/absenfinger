<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan PKL - {{ $pkl->student->name ?? 'Siswa' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    @php
        $isLegal = ($paperSize ?? 'legal') === 'legal';
        $pageWidth = $isLegal ? '355.6mm' : '297mm';
        $pageHeight = $isLegal ? '215.9mm' : '210mm';
        $pageSizeCSS = $isLegal ? 'Legal landscape' : 'A4 landscape';
    @endphp
    <style>
        @page {
            size: {{ $pageSizeCSS }};
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13pt;
            line-height: 1.4;
            background: #f0f0f0;
        }

        .page {
            width: {{ $pageWidth }};
            height: {{ $pageHeight }};
            background: white;
            margin: 10px auto;
            position: relative;
            overflow: hidden;
        }

        /* Border decoration - only show if no background */
        .border-outer {
            position: absolute;
            top: 5mm;
            left: 5mm;
            right: 5mm;
            bottom: 5mm;
            @if(!$bgFront && !$bgBack)
                border: 8px solid;
                border-image: repeating-linear-gradient(45deg,
                        #1e40af,
                        #1e40af 10px,
                        #fbbf24 10px,
                        #fbbf24 20px,
                        #dc2626 20px,
                        #dc2626 30px,
                        #fbbf24 30px,
                        #fbbf24 40px) 8;
            @else border: none;
            @endif
        }

        /* Background image container - full page coverage */
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg-image img {
            width: 100%;
            height: 100%;
            object-fit: fill;
        }

        .border-inner {
            position: absolute;
            top: 12mm;
            left: 12mm;
            right: 12mm;
            bottom: 12mm;
            z-index: 1;
            @if(!$bgFront && !$bgBack)
                border: 3px solid #dc2626;
            @else border: none;
            @endif
        }

        .content {
            position: absolute;
            top: 25mm;
            left: 25mm;
            right: 25mm;
            bottom: 18mm;
            padding: 5mm;
            z-index: 2;
        }

        /* Front page specific - larger margins */
        .front-page .content {
            top: 22mm;
            left: 35mm;
            right: 35mm;
        }

        /* Front Page */
        .front-page .title {
            text-align: center;
            font-family: 'Oswald', 'Arial Narrow', 'Impact', sans-serif;
            color: #000000ff;
            font-size: 28pt;
            font-weight: 700;
            margin-bottom: 15px;
            text-decoration-color: #dc2626;
            text-decoration-thickness: 3px;
            text-underline-offset: 5px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .front-page .intro {
            margin-bottom: 10px;
            font-size: 13pt;
        }

        .front-page .info-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .front-page .info-table td {
            padding: 2px 5px;
            vertical-align: top;
            font-size: 13pt;
        }

        .front-page .info-table .label {
            width: 180px;
        }

        .front-page .info-table .colon {
            width: 15px;
        }

        .front-page .info-table .value {
            text-transform: uppercase;
        }

        .front-page .description {
            margin: 15px 0;
            text-align: justify;
            font-size: 13pt;
            line-height: 1.5;
        }

        .front-page .signature {
            width: 250px;
            text-align: left;
            margin-top: 20px;
            margin-left: 55%;
        }

        .front-page .signature .name {
            margin-top: 70px;
            font-weight: bold;
        }

        /* Back Page */
        .back-page .title {
            text-align: left;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .back-page .subtitle {
            text-align: left;
            font-size: 12pt;
            margin-bottom: 10px;
        }

        .back-page .tables-container {
            display: flex;
            gap: 10px;
        }

        .back-page .table-section {
            flex: 1;
        }

        .back-page table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .back-page table th,
        .back-page table td {
            border: 1px solid #000;
            padding: 3px 5px;
        }

        .back-page table th {
            background: #cfcfcdff;
            font-weight: bold;
            text-align: center;
        }

        .back-page table .number {
            width: 25px;
            text-align: center;
        }

        .back-page table .nilai {
            width: 45px;
            text-align: center;
        }

        .back-page .section-title {
            background: #cfcfcdff;
            font-weight: bold;
            text-align: center;
        }

        .back-page .avg-row {
            background: #cfcfcdff;
            font-weight: bold;
        }

        .back-page .final-row {
            background: #cfcfcdff;
            font-weight: bold;
        }

        .back-page .legend {
            margin-top: 10px;
            font-size: 12pt;
        }

        .back-page .legend table {
            width: auto;
            float: right;
            margin-left: 20px;
        }

        .back-page .signature-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 130px;
            margin-right: 80px;
            font-size: 12pt;
        }

        .back-page .signature-box {
            text-align: left;
            width: 230px;
        }

        .back-page .signature-box .name {
            margin-top: 50px;
            font-weight: bold;
        }

        @media print {
            body {
                background: white;
            }

            .page {
                margin: 0;
                page-break-after: always;
            }

            .page:last-child {
                page-break-after: auto;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Print Button -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #1e40af; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">
            🖨️ Cetak Surat
        </button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; margin-left: 10px;">
            ✖️ Tutup
        </button>
    </div>

    <!-- Front Page -->
    <div class="page front-page">
        @if($bgFront)
            <div class="bg-image">
                <img src="{{ asset('storage/' . $bgFront) }}" alt="Background">
            </div>
        @endif
        <div class="border-outer"></div>
        <div class="border-inner"></div>
        <div class="content">
            <h1 class="title">SURAT KETERANGAN PRAKTIK KERJA LAPANGAN</h1>
            <br>
            <p class="intro">Pimpinan <strong>{{ $pkl->dudi->nama ?? 'DUDI' }}</strong></p>
            <p class="intro">Memberikan surat keterangan kepada :</p>

            <table class="info-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pkl->student->name ?? 'SISWA' }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat, tanggal Lahir</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pkl->student->tmpt_lhr ?? '-' }},
                        {{ $pkl->student->tgl_lhr ? \Carbon\Carbon::parse($pkl->student->tgl_lhr)->translatedFormat('d F Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Konsentrasi Keahlian</td>
                    <td class="colon">:</td>
                    <td class="value">
                        {{ $pkl->student->jurusan->paket_keahlian ?? 'TEKNIK JARINGAN KOMPUTER DAN TELEKOMUNIKASI' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Sekolah Asal</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $setting->school_name ?? 'SMKN 1 Seputih Agung Lampung Tengah' }}</td>
                </tr>
            </table>

            <div class="description">
                <p>Telah melaksanakan Praktik Kerja Lapangan di
                    <strong>{{ $pkl->dudi->nama ?? 'DUDI' }}</strong>
                </p>
                <p>Dari Tanggal
                    {{ $sertifikat && $sertifikat->tgl_mulai ? $sertifikat->tgl_mulai->translatedFormat('d F Y') : '-' }}
                    s.d.
                    {{ $sertifikat && $sertifikat->tgl_selesai ? $sertifikat->tgl_selesai->translatedFormat('d F Y') : '-' }}
                    dengan predikat <strong style="color: #000000;">{{ $predikat }}</strong>
                </p>
                <p style="margin-top: 8px;">Surat keterangan ini dikeluarkan sebagai bukti telah melaksanakan Praktik
                    Kerja Lapangan.</p>
            </div>

            <div class="signature">
                <p>{{ $pkl->dudi->alamat ?? 'KOTA' }},
                    {{ $sertifikat && $sertifikat->tgl_cetak ? $sertifikat->tgl_cetak->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                </p>
                <p>Pimpinan {{ $pkl->dudi->nama ?? 'DUDI' }}</p>
                <p class="name">{{ $pkl->pimpinan ?? 'PIMPINAN' }}</p>
            </div>

            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- Back Page -->
    <div class="page back-page">
        @if($bgBack)
            <div class="bg-image">
                <img src="{{ asset('storage/' . $bgBack) }}" alt="Background">
            </div>
        @endif
        <div class="border-outer"></div>
        <div class="border-inner"></div>
        <div class="content">
            <h2 class="title">DAFTAR NILAI PRAKTIK KERJA LAPANGAN KONSENTRASI KEAHLIAN
                {{ strtoupper($pkl->student->jurusan->paket_keahlian ?? 'TEKNIK JARINGAN KOMPUTER DAN TELEKOMUNIKASI') }}
            </h2>
            <p class="subtitle">NAMA SISWA : <strong>{{ $pkl->student->name ?? '-' }}</strong></p>

            <div class="tables-container">
                <!-- Left Table: Soft Skills -->
                <div class="table-section">
                    <table>
                        <thead>
                            <tr>
                                <th class="number">NO</th>
                                <th>KOMPONEN YANG DINILAI</th>
                                <th class="nilai">NILAI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="section-title">
                                <td colspan="3" align="left">1. ASPEK SOFT SKILLS</td>
                            </tr>
                            @php $softIndex = 0; @endphp
                            @forelse($pkl->softNilai as $index => $nilai)
                                @php $softIndex++; @endphp
                                <tr>
                                    <td class="number">{{ chr(96 + $softIndex) }}.</td>
                                    <td>{{ $nilai->komponenSoft->nama ?? 'Komponen' }}</td>
                                    <td class="nilai">{{ number_format($nilai->nilai, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center;">Belum ada data</td>
                                </tr>
                            @endforelse
                            <tr class="avg-row">
                                <td colspan="2" style="text-align: left;">NILAI RATA-RATA 1</td>
                                <td class="nilai">{{ number_format($avgSoft, 0) }}</td>
                            </tr>

                            <!-- Wirausaha Section -->
                            <tr class="section-title">
                                <td colspan="3" align="left">3. ASPEK KEMANDIRIAN BERWIRAUSAHA</td>
                            </tr>
                            @php $wirausahaIndex = 0; @endphp
                            @forelse($pkl->wirausahaNilai as $index => $nilai)
                                @php $wirausahaIndex++; @endphp
                                <tr>
                                    <td class="number">{{ chr(96 + $wirausahaIndex) }}.</td>
                                    <td>{{ $nilai->komponenWirausaha->nama ?? 'Komponen' }}</td>
                                    <td class="nilai">{{ number_format($nilai->nilai, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center;">Belum ada data</td>
                                </tr>
                            @endforelse
                            <tr class="avg-row">
                                <td colspan="2" style="text-align: left;">NILAI RATA-RATA 3</td>
                                <td class="nilai">{{ number_format($avgWirausaha, 0) }}</td>
                            </tr>

                            <tr class="final-row">
                                <td colspan="2" style="text-align: left;">NILAI AKHIR (NH1x40%+NH2x50%+NH3x10%)</td>
                                <td class="nilai">{{ number_format($finalGrade, 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Right Table: Hard Skills -->
                <div class="table-section">
                    <table>
                        <thead>
                            <tr>
                                <th class="number">NO</th>
                                <th>KOMPONEN YANG DINILAI</th>
                                <th class="nilai">NILAI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="section-title">
                                <td colspan="3" align="left">2. ASPEK HARD SKILLS</td>
                            </tr>
                            @php $hardIndex = 0; @endphp
                            @forelse($pkl->hardNilai as $index => $nilai)
                                @php $hardIndex++; @endphp
                                <tr>
                                    <td class="number">{{ chr(96 + $hardIndex) }}.</td>
                                    <td>{{ $nilai->komponenHard->nama ?? 'Komponen' }}</td>
                                    <td class="nilai">{{ number_format($nilai->nilai, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center;">Belum ada data</td>
                                </tr>
                            @endforelse
                            <tr class="avg-row">
                                <td colspan="2" style="text-align: left;">NILAI RATA-RATA 2</td>
                                <td class="nilai">{{ number_format($avgHard, 0) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Signature -->
                    <div class="signature-section">
                        <div class="signature-box">
                            <p>{{ $pkl->dudi->alamat ?? 'Kota' }},
                                {{ $sertifikat && $sertifikat->tgl_cetak ? $sertifikat->tgl_cetak->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                            </p>
                            <p>Pembimbing Industri</p>
                            <p class="name">{{ $pkl->pembimbing_industri ?? 'Pembimbing' }}</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Legend - Full width centered at bottom -->
            <div class="legend" style="margin-top: 50px; font-size: 12pt; text-align: left;">
                <p style="margin-bottom: 5px;"><strong>Keterangan Predikat:</strong></p>
                <p>90-100 = Amat Baik (A) &nbsp;|&nbsp; 80-89 = Baik (B) &nbsp;|&nbsp; 70-79 = Cukup (C) &nbsp;|&nbsp;
                    60-69 = Kurang (D) &nbsp;|&nbsp; 0-59 = Sangat Kurang (E)</p>
            </div>
        </div>
    </div>
</body>

</html>