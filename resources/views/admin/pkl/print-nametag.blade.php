<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Nametag PKL</title>
    <style>
        @page {
            margin: 0.5cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color: #1f2937;
        }

        .container {
            width: 100%;
        }

        .row-container {
            width: 100%;
            clear: both;
            margin-bottom: 0.5cm;
            /* display: flex removed for compatibility */
        }

        .nametag {
            width: 6cm;
            height: 10.5cm;
            position: relative;
            background: #fff;
            page-break-inside: avoid;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-top: 5px solid #1e3a8a;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            float: left;
            /* Reverted to float */
            margin-right: 0.5cm;
        }

        .nametag:nth-child(3n),
        .last-item {
            margin-right: 0 !important;
        }

        /* --- HEADER --- */
        .header-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3.2cm;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
            clip-path: polygon(0 0, 100% 0, 100% 80%, 0 100%);
            z-index: 1;
        }

        .header-bg-accent {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3.4cm;
            background: rgba(59, 130, 246, 0.3);
            clip-path: polygon(0 0, 100% 0, 100% 80%, 0 100%);
            transform: translateY(3px);
            z-index: 0;
        }

        .header-content {
            position: absolute;
            top: 0.4cm;
            width: 100%;
            text-align: center;
            z-index: 5;
            color: #fff;
        }

        .school-name {
            font-size: 10pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            padding: 0 5px;
        }

        .card-type {
            font-size: 6pt;
            font-weight: 500;
            opacity: 0.9;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- PHOTO --- */
        .photo-container {
            position: absolute;
            top: 1.6cm;
            left: 50%;
            transform: translateX(-50%);
            width: 3.2cm;
            height: 4cm;
            background-color: #fff;
            border-radius: 8px;
            z-index: 10;
            padding: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .photo-inner {
            width: 100%;
            height: 100%;
            border-radius: 6px;
            overflow: hidden;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
        }

        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
        }

        .photo-placeholder {
            font-size: 8pt;
            color: #9ca3af;
            font-weight: 600;
        }

        /* --- INFO CONTENT --- */
        .info-container {
            position: absolute;
            top: 6cm;
            width: 100%;
            text-align: center;
            z-index: 5;
        }

        .student-name {
            font-size: 12pt;
            font-weight: 800;
            color: #111827;
            margin-bottom: 2px;
            line-height: 1.2;
            padding: 0 5px;
            text-transform: uppercase;
        }

        .student-role {
            font-size: 8pt;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Key-Value Details */
        .details-table {
            width: 90%;
            margin: 0 auto;
            font-size: 7pt;
            text-align: left;
            padding: 5px 10px;
            background: #f9fafb;
            border-radius: 6px;
        }

        .details-row {
            display: flex;
            margin-bottom: 3px;
            align-items: baseline;
        }

        .details-label {
            color: #4b5563;
            font-weight: 700;
            width: 45px;
            flex-shrink: 0;
        }

        .details-value {
            color: #111827;
            font-weight: 600;
            flex: 1;
            word-break: break-word;
        }

        /* --- FOOTER --- */
        .barcode-container {
            position: absolute;
            bottom: 0.8cm;
            width: 100%;
            text-align: center;
        }

        .barcode-bars {
            display: inline-block;
            height: 0.6cm;
            width: 70%;
            background: repeating-linear-gradient(90deg,
                    #000,
                    #000 1px,
                    transparent 1px,
                    transparent 3px,
                    #111 3px,
                    #111 5px);
            opacity: 0.8;
        }

        .footer-url {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 0.6cm;
            line-height: 0.6cm;
            background-color: #1e3a8a;
            text-align: center;
            font-size: 6pt;
            color: #fff;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="container">
        @foreach($pkls->chunk(3) as $chunk)
            <div class="row-container clearfix">
                @foreach($chunk as $pkl)
                    <div class="nametag {{ $loop->last ? 'last-item' : '' }}">
                        <!-- Background Shapes -->
                        <div class="header-bg-accent"></div>
                        <div class="header-bg"></div>

                        <!-- Header Info -->
                        <div class="header-content">
                            <div class="school-name">{{ Str::limit($schoolName, 25) }}</div>
                            <div class="card-type">KARTU PESERTA PKL</div>
                        </div>

                        <!-- Photo -->
                        <div class="photo-container">
                            <div class="photo-inner">
                                @if($pkl->student->user && $pkl->student->user->foto)
                                    <img src="{{ public_path('storage/' . $pkl->student->user->foto) }}" class="photo-img"
                                        alt="Foto"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div class="photo-placeholder" style="display:none;">FOTO</div>
                                @elseif($pkl->student->photo_path)
                                    <img src="{{ public_path('storage/' . $pkl->student->photo_path) }}" class="photo-img"
                                        alt="Foto"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div class="photo-placeholder" style="display:none;">FOTO</div>
                                @else
                                    <div class="photo-placeholder">FOTO</div>
                                @endif
                            </div>
                        </div>

                        <!-- Main Text -->
                        <div class="info-container">
                            <div class="student-name">{{ Str::limit($pkl->student->name, 18) }}</div>
                            <div class="student-role">PESERTA PKL</div>

                            <div class="details-table">
                                <div class="details-row">
                                    <span class="details-label">ID</span>
                                    <span class="details-value">: {{ $pkl->student->nis }}</span>
                                </div>
                                <div class="details-row">
                                    <span class="details-label">Kelas</span>
                                    <span class="details-value">: {{ $pkl->student->kelas->nm_kls ?? '-' }}</span>
                                </div>
                                <div class="details-row">
                                    <span class="details-label">DUDI</span>
                                    <span class="details-value">: {{ Str::limit($pkl->dudi->nama, 22) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="barcode-container">
                            <div class="barcode-bars"></div>
                        </div>
                        <div class="footer-url">
                            {{ $schoolName ?? 'SMK NEGERI 1 SEPUTIH AGUNG' }}
                        </div>

                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</body>

</html>