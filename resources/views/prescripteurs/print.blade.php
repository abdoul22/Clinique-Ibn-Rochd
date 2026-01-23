<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Prescripteurs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            padding: 20px;
            background: white;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #333;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
            font-weight: bold;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }

        .btn-container {
            margin-bottom: 20px;
            text-align: right;
        }

        .btn {
            padding: 10px 20px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        @media print {
            .btn-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="btn-container">
        <a href="{{ route('prescripteurs.index') }}" class="btn btn-secondary">← Retour</a>
        <button onclick="window.print()" class="btn btn-primary">🖨 Imprimer</button>
    </div>

    <div class="header">
        <h1>{{ config('clinique.name', 'Clinique') }}</h1>
        <p>Liste des Prescripteurs</p>
        <p style="font-size: 12px; margin-top: 5px;">Date d'impression: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Spécialité</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prescripteurs as $prescripteur)
            <tr>
                <td>{{ $prescripteur->id }}</td>
                <td><strong>{{ $prescripteur->nom }}</strong></td>
                <td>{{ $prescripteur->specialite ?? 'Non spécifiée' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; padding: 30px; color: #999;">
                    Aucun prescripteur trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ config('clinique.name', 'Clinique') }} - {{ config('clinique.address', 'Nouakchott, Mauritanie') }}</p>
        <p>Tél: {{ config('clinique.phone', 'N/A') }} - Email: {{ config('clinique.email', 'N/A') }}</p>
    </div>
</body>
</html>






