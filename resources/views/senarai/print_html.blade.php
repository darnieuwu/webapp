<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Senarai Aduan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
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
            margin-bottom: 20px;
        }        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 12px;
            vertical-align: middle;
        }
        table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .align-middle {
            vertical-align: middle;
        }
        ul {
            padding-left: 15px;
            margin: 0;
        }
        li {
            margin-bottom: 3px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
        }
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-button:hover {
            background-color: #0056b3;
        }
        
        @media print {
            .print-controls {
                display: none;
            }
            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="print-button" onclick="window.print();">Cetak Halaman</button>
    </div>

    <div class="header">
        <h2>LAPORAN SENARAI ADUAN</h2>
        <p>{{ Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bil.</th>
                <th>Tarikh Aduan</th>
                <th>Nama PPK</th>
                <th>Cawangan</th>
                <th>Peralatan</th>
                <th>No. Siri</th>
                <th>Model</th>
                <th>Aduan</th>
                <th>Penyelesaian</th>
                <th>Tarikh Hantar Baikpulih</th>
                <th>Vendor</th>
                <th>Tarikh Selesai Baikpulih</th>
                <th>Tarikh Hantar Cawangan</th>
                <th>Catatan</th>
                <th>Kos (RM)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>            
            @foreach ($senarais as $index => $senarai)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $senarai->tarikh_aduan->format('d/m/Y') }}</td>
                <td>{{ $senarai->ppk_name }}</td>
                <td class="text-center">{{ $senarai->cawangan_name }}</td>
                <td class="text-center">{{ $senarai->peralatan_name }}</td>
                <td class="text-center">{{ $senarai->no_siri }}</td>                
                <td class="text-center">{{ $senarai->modelan_name }}</td>
                <td class="align-middle">
                    <ul class="pl-3 mb-0" style="text-align: left;">
                        @foreach ($senarai->aduanArray as $item)
                            @if(trim($item) !== '')
                                <li>{{ trim($item) }}</li>
                            @endif
                        @endforeach
                    </ul>
                </td>
                <td class="align-middle">
                    <ul class="pl-3 mb-0" style="text-align: left;">
                        @foreach ($senarai->penyelesaianArray as $item)
                            @if(trim($item) !== '')
                                <li>{{ trim($item) }}</li>
                            @endif
                        @endforeach
                    </ul>
                </td>
                <td class="text-center">{{ $senarai->tarikh_hantar_baikpulih ? $senarai->tarikh_hantar_baikpulih->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $senarai->vendor_name ?? '-' }}</td>
                <td class="text-center">{{ $senarai->tarikh_selesai_baikpulih ? $senarai->tarikh_selesai_baikpulih->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $senarai->tarikh_hantar_cawangan ? $senarai->tarikh_hantar_cawangan->format('d/m/Y') : '-' }}</td>
                <td>{{ $senarai->catatan }}</td>
                <td class="text-center">{{ $senarai->kos ? number_format($senarai->kos, 2) : '0.00' }}</td>
                <td class="text-center">{{ $senarai->status->nama }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto open print dialog if not coming from a print preview
            if (!window.location.search.includes('no-print')) {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>
</html>
