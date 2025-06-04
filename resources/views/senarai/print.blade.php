
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Senarai Aduan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }        table th, table td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 10px;
            vertical-align: middle;
        }
        table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .align-middle {
            vertical-align: middle;
        }
        ul {
            margin: 0;
            padding-left: 15px;
        }
        li {
            font-size: 9px;
            line-height: 1.2;
            margin-bottom: 2px;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2> LAPORAN SENARAI ADUAN</h2>
        <p>{{ Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bil.</th>
                <th>Tarikh Aduan</th>
                <th>Cawangan</th>
                <th>No. Siri</th>
                <th>Model</th>
                <th>Aduan</th>
                <th>Catatan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>            
            @foreach ($senarais as $index => $senarai)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $senarai->tarikh_aduan->format('d/m/Y') }}</td>
                <td class="text-center">{{ $senarai->cawangan_name }}</td>                
                <td class="text-center">{{ $senarai->no_siri }}</td>                
                <td class="text-center">{{ $senarai->modelan_name }}</td>
                <td class="align-middle">
                    <ul style="padding-left: 15px; margin: 0; text-align: left;">
                        @foreach ($senarai->aduanArray as $item)
                            @if(trim($item) !== '')
                                <li style="font-size: 9px; line-height: 1.2;">{{ trim($item) }}</li>
                            @endif
                        @endforeach
                    </ul>
                </td>
                <td class="align-middle">{{ $senarai->catatan }}</td>
                <td class="text-center">{{ $senarai->status->nama }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>