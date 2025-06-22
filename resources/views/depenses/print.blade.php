<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Impression Dépenses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body onload="window.print()">
    <h2>Liste des dépenses</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
            </tr>
        </thead>
        <tbody>
            @foreach($depenses as $depense)
            <tr>
                <td>{{ $depense->id }}</td>
                <td>{{ $depense->nom }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
