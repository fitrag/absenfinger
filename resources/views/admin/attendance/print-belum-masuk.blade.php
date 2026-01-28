<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa Belum Masuk - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</title>
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
            padding: 20px 40px;
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
            padding: 3px 6px;
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

        .status-belum-masuk {
            color: #ea580c;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .footer p {
            margin-bottom: 60px;
        }

        .empty-message {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $schoolName }}</h1>
        <h2>DAFTAR SISWA BELUM ABSEN MASUK</h2>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah Siswa Belum Masuk</strong></td>
                <td>: {{ count($belumMasukStudents) }} orang</td>
            </tr>
        </table>
    </div>

    @if(count($belumMasukStudents) > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 80px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 100px;">Kelas</th>
                    <th style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($belumMasukStudents as $index => $student)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $student['nis'] }}</td>
                        <td>{{ $student['name'] }}</td>
                        <td class="center">{{ $student['kelas'] }}</td>
                        <td class="center status-belum-masuk" style="white-space: nowrap;">-</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-message">
            Semua siswa sudah absen masuk pada tanggal ini.
        </div>
    @endif

    <div class="footer">
        <p>{{ $city }}, {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
        <p>Guru Piket</p>
        <br><br><br>
        <p>_________________________</p>
    </div>
</body>

</html>