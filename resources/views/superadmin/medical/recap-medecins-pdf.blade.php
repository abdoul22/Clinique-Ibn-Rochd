<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récapitulatif Médecins - {{ date('d/m/Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #333;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #6366f1;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }
        tr:hover {
            background: #f9fafb;
        }
        .medecin-name {
            font-weight: bold;
            color: #333;
        }
        .specialite {
            color: #666;
            font-size: 10px;
        }
        .number {
            text-align: center;
            font-weight: bold;
            color: #6366f1;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 10px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RÉCAPITULATIF MÉDECINS</h1>
        <p>Clinique Humanité - {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Médecin</th>
                <th style="text-align: center;">Consultations<br>Mois</th>
                <th style="text-align: center;">Consultations<br>Total</th>
                <th style="text-align: center;">Revenus<br>Mois (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medecinStats as $stat)
                <tr>
                    <td>
                        <div class="medecin-name">
                            {{ $stat['medecin']->fonction }} {{ $stat['medecin']->nom }} {{ $stat['medecin']->prenom }}
                        </div>
                        <div class="specialite">{{ $stat['medecin']->specialite }}</div>
                    </td>
                    <td class="number">{{ $stat['consultations_mois'] }}</td>
                    <td class="number">{{ $stat['consultations_total'] }}</td>
                    <td class="number">{{ number_format($stat['revenus_mois'], 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Clinique Humanité - Document généré le {{ date('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>


