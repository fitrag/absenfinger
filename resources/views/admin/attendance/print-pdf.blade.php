<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir Kelas {{ $kelas->nm_kls }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .info {
            margin-bottom: 15px;
        }

        .info table {
            border-collapse: collapse;
        }

        .info td {
            padding: 2px 10px 2px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        table.data th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        table.data td.center {
            text-align: center;
        }

        .status-hadir {
            color: #16a34a;
        }

        .status-terlambat {
            color: #d97706;
        }

        .status-sakit {
            color: #7c3aed;
        }

        .status-izin {
            color: #0891b2;
        }

        .status-alpha {
            color: #dc2626;
        }

        .status-belum {
            color: #6b7280;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .footer p {
            margin-bottom: 60px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $settings->school_name ?? 'SMK NEGERI 1' }}</h1>
        <h2>DAFTAR HADIR SISWA</h2>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>: {{ $kelas->nm_kls }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah Siswa</strong></td>
                <td>: {{ $students->count() }} orang</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">NIS</th>
                <th>Nama Siswa</th>
                <th style="width: 70px;">Jam Masuk</th>
                <th style="width: 70px;">Jam Pulang</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php
                    $attendance = $attendanceData[$student->nis] ?? null;
                    $checkIn = $attendance['check_in'] ?? null;
                    $checkOut = $attendance['check_out'] ?? null;

                    $status = 'Belum Absen';
                    $statusClass = 'status-belum';
                    $jamMasuk = '-';
                    $jamPulang = '-';

                    if ($checkIn) {
                        if ($checkIn->checktype == 2) {
                            $status = 'Sakit';
                            $statusClass = 'status-sakit';
                        } elseif ($checkIn->checktype == 3) {
                            $status = 'Izin';
                            $statusClass = 'status-izin';
                        } elseif ($checkIn->checktype == 4) {
                            $status = 'Alpha';
                            $statusClass = 'status-alpha';
                        } else {
                            $jamMasuk = $checkIn->checktime->format('H:i');
                            $isLate = $checkIn->checktime->format('H:i') > '07:00';
                            if ($checkOut) {
                                $jamPulang = $checkOut->checktime->format('H:i');
                                $status = $isLate ? 'Terlambat' : 'Hadir';
                                $statusClass = $isLate ? 'status-terlambat' : 'status-hadir';
                            } else {
                                $status = 'Bolos';
                                $statusClass = 'status-alpha';
                            }
                        }
                    }
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $student->nis }}</td>
                    <td>{{ $student->name }}</td>
                    <td class="center">{{ $jamMasuk }}</td>
                    <td class="center">{{ $jamPulang }}</td>
                    <td class="center {{ $statusClass }}"><strong>{{ $status }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $settings->city ?? 'Seputih Agung' }}, {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
        <p>Wali Kelas</p>
        <br><br><br>
        <p>_________________________</p>
    </div>
</body>

</html>