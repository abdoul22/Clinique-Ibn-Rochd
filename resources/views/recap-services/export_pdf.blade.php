{{-- resources/views/recapitulatif_service_journiers/export_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>PDF - Récapitulatif journalier des services</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center">Récapitulatif journalier des services</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Service</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recaps as $recap)
            <tr>
                <td>{{ $recap->id }}</td>
                <td>{{ $recap->service->nom ?? '—' }}</td>
                <td>{{ number_format($recap->total, 0, ',', ' ') }} MRU</td>
                <td>{{ $recap->date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
