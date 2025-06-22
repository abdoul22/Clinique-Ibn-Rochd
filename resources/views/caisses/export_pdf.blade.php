<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Export PDF - Caisses</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h2>Liste des entrées en caisse</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>N° Entrée</th>
                <th>Patient</th>
                <th>Examinateur</th>
                <th>Date</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caisses as $caisse)
            <tr>
                <td>{{ $caisse->id }}</td>
                <td>{{ $caisse->numero_entre }}</td>
                <td>{{ $caisse->patient->nom ?? '—' }}</td>
                <td>{{ $caisse->medecin->nom ?? '—' }}</td>
                <td>{{ $caisse->date_examen }}</td>
                <td>{{ number_format($caisse->total, 0, ',', ' ') }} MRU</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
