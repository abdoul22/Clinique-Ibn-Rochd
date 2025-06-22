<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Dépenses PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 8px;
        }
    </style>
</head>

<body>
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
