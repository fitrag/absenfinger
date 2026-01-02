<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Kelas {{ $kelas->nm_kls }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 30px 60px 30px 90px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 14px;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 12px;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .info {
            margin-bottom: 10px;
        }

        .info table td {
            padding: 2px 5px 2px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
        }

        table.data th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }

        table.data td.left {
            text-align: left;
        }

        table.data td.jam {
            width: 22px;
            min-width: 22px;
        }

        .footer {
            margin-top: 25px;
        }

        .footer-table {
            width: 100%;
        }

        .footer-table td {
            vertical-align: top;
            padding: 5px 10px;
        }

        .sign-box {
            text-align: center;
        }

        .sign-box p {
            margin-bottom: 5px;
        }

        .dots {
            line-height: 1.8;
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
                <td style="padding-left: 20px;"><strong>Hari/Tanggal</strong></td>
                <td>: {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}</td>
            </tr>

        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 22px;">No</th>
                <th style="width: 65px;">NIS</th>
                <th style="width: auto;">Nama Siswa</th>
                <th style="width: 25px;">L/P</th>
                <th class="jam">1</th>
                <th class="jam">2</th>
                <th class="jam">3</th>
                <th class="jam">4</th>
                <th class="jam">5</th>
                <th class="jam">6</th>
                <th class="jam">7</th>
                <th class="jam">8</th>
                <th class="jam">9</th>
                <th class="jam">10</th>
                <th style="width: 40px;">Ket</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-size: 9px;">{{ $student->nis }}</td>
                    <td class="left" style="font-size: 9px;">{{ $student->name }}</td>
                    <td>{{ $student->jen_kel }}</td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td class="jam"></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="margin-bottom: 10px;">
            <strong>Jumlah Siswa:</strong> {{ $students->count() }} siswa &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>L (Laki-laki):</strong> {{ $students->where('jen_kel', 'L')->count() }} siswa
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>P (Perempuan):</strong> {{ $students->where('jen_kel', 'P')->count() }} siswa
        </div>
        <table class="footer-table">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <table style="width: auto;">
                        <tr>
                            <td style="width: auto;"><strong>Guru Mata Pelajaran</strong></td>
                            <td style="width: auto; padding-left: 10px;"><strong>TTD</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;">1. ...............................</td>
                            <td style="padding: 3px 0 3px 10px;">...........</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;">2. ...............................</td>
                            <td style="padding: 3px 0 3px 10px;">...........</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;">3. ...............................</td>
                            <td style="padding: 3px 0 3px 10px;">...........</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;">4. ...............................</td>
                            <td style="padding: 3px 0 3px 10px;">...........</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <div class="sign-box">
                        <p>{{ $settings->city ?? 'Seputih Agung' }},
                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}
                        </p>
                        <br>
                        <p><strong>Wali Kelas</strong></p>
                        <br><br><br><br>
                        <p><u>{{ $walas && $walas->guru ? $walas->guru->nama : '_______________________' }}</u></p>
                        <p>NIP. {{ $walas && $walas->guru ? $walas->guru->nip : '_______________________' }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>