<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Data Dudi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <h1>Data Dunia Usaha dan Dunia Industri (DUDI)</h1>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 30%">Nama</th>
                <th style="width: 35%">Alamat</th>
                <th style="width: 15%">Telepon</th>
                <th style="width: 15%">Bidang Usaha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dudis as $index => $dudi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $dudi->nama }}</td>
                    <td>{{ $dudi->alamat ?? '-' }}</td>
                    <td>{{ $dudi->telepon ?? '-' }}</td>
                    <td>{{ $dudi->bidang_usaha ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>

</html>