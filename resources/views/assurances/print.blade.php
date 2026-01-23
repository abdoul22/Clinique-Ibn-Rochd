<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Impression des assurances</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <h2>Liste des assurances</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Crédit Assurance (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assurances as $assurance)
            @php
                $creditAssurance = \App\Models\Caisse::where('assurance_id', $assurance->id)
                    ->where('couverture', '>', 0)
                    ->get()
                    ->sum(function($caisse) {
                        return $caisse->total * ($caisse->couverture / 100);
                    });
            @endphp
            <tr>
                <td>{{ $assurance->id }}</td>
                <td>{{ $assurance->nom }}</td>
                <td>{{ number_format($creditAssurance, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="no-print" style="margin-top: 30px; text-align: center; padding: 20px;">
        <a href="{{ route('assurances.index') }}" 
           style="display: inline-block; background: #6b7280; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-size: 16px; margin-right: 10px;">
            ← Retour
        </a>
        <button onclick="window.print()"
            style="background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px;">
            🖨️ Imprimer
        </button>
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>

</html>
