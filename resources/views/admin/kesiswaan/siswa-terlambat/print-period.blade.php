<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keterlambatan - {{ $kelasInfo ? $kelasInfo->nm_kls : 'Semua Kelas' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.1;
            padding: 15px 50px 15px 100px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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

        .student-header {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .student-header td {
            padding: 6px !important;
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

        .summary-box {
            margin-top: 20px;
            padding: 15px;
            border: 2px solid #f59e0b;
            background-color: #fffbeb;
        }

        .summary-box h3 {
            color: #d97706;
            margin-bottom: 10px;
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
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #f43f5e;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background-color: #e11d48;
        }

        .warning-row {
            background-color: #fee2e2;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>{{ $settings['school_name'] ?? 'SMK NEGERI 1 SEPUTIH AGUNG' }}</h1>
        <h2>LAPORAN KETERLAMBATAN SISWA</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s.d
            {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
        </p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>Kelas</td>
                <td>: {{ $kelasInfo ? $kelasInfo->nm_kls : 'Semua Kelas' }}</td>
            </tr>
            <tr>
                <td>Jumlah Siswa Terlambat</td>
                <td>: <strong>{{ $totalStudents }} siswa</strong></td>
            </tr>
            <tr>
                <td>Total Keterlambatan</td>
                <td>: <strong style="color: #d97706;">{{ $totalRecords }}x ({{ $totalMinutes }} menit)</strong></td>
            </tr>
        </table>
    </div>

    @if($groupedData->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 80px;">Tanggal</th>
                    <th style="width: 55px;">Jam Datang</th>
                    <th style="width: 55px;">Terlambat</th>
                    <th>Alasan</th>
                    <th style="width: 60px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($groupedData as $group)
                    <tr class="student-header {{ $group->total_terlambat >= 3 ? 'warning-row' : '' }}">
                        <td colspan="6">
                            <span style="color: {{ $group->total_terlambat >= 3 ? '#dc2626' : '#000' }};">
                                {{ $group->student->name }}
                            </span>
                            <span style="font-weight: normal; font-size: 9px; color: #666;">
                                ({{ $group->student->nis }} • {{ $group->student->kelas->nm_kls ?? '-' }})
                            </span>
                            <span style="float: right; color: #d97706;">
                                {{ $group->total_terlambat }}x terlambat • {{ $group->total_menit }} menit
                            </span>
                        </td>
                    </tr>
                    @foreach($group->items as $item)
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
                            <td class="center">{{ $no++ }}</td>
                            <td class="center">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="center">{{ \Carbon\Carbon::parse($item->jam_datang)->format('H:i') }}</td>
                            <td class="center" style="color: #d97706; font-weight: bold;">{{ $item->keterlambatan_menit }} mnt</td>
                            <td>{{ $item->alasan ?? '-' }}</td>
                            <td class="center {{ $statusClass }}"><strong>{{ $statusLabel }}</strong></td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        @if($groupedData->where('total_terlambat', '>=', 3)->count() > 0)
            <div class="summary-box">
                <h3>⚠️ Perhatian</h3>
                <p>Terdapat <strong>{{ $groupedData->where('total_terlambat', '>=', 3)->count() }} siswa</strong> yang terlambat
                    3 kali atau lebih dan memerlukan pembinaan:</p>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    @foreach($groupedData->where('total_terlambat', '>=', 3) as $group)
                        <li>{{ $group->student->name }} ({{ $group->student->kelas->nm_kls ?? '-' }}) -
                            {{ $group->total_terlambat }}x
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Tidak ada data keterlambatan pada periode ini.</p>
        </div>
    @endif

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