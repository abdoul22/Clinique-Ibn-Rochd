<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste de Mes Patients - Dr. {{ $medecin->nom_complet_avec_prenom }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background-color: #e3f2fd;
            padding: 15px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #f5f9ff;
            padding: 20px;
            border: 1px solid #90caf9;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1e40af;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 10px;
        }
        .medecin-info {
            background-color: #fff;
            border: 1px solid #90caf9;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .medecin-info strong {
            color: #1e40af;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #fff;
        }
        thead {
            background-color: #1e40af;
            color: white;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #90caf9;
            font-size: 10px;
        }
        th {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        tbody tr:nth-child(even) {
            background-color: #f5f9ff;
        }
        tbody tr:hover {
            background-color: #e3f2fd;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #90caf9;
        }
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-box {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #90caf9;
            text-align: center;
        }
        .stat-box .number {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
        }
        .stat-box .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>📋 LISTE DE MES PATIENTS</h1>
            <p>{{ config('clinique.name') }}</p>
            <p>{{ config('clinique.address') }}</p>
            <p>{{ config('clinique.phone') }}</p>
        </div>

        <!-- Informations Médecin -->
        <div class="medecin-info">
            <strong>Médecin :</strong> Dr. {{ $medecin->nom_complet_avec_prenom }}<br>
            <strong>Spécialité :</strong> {{ $medecin->specialite ?? 'Médecin' }}<br>
            <strong>Date d'impression :</strong> {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}
        </div>

        <!-- Statistiques -->
        <div class="stats">
            <div class="stat-box">
                <div class="number">{{ $patients->count() }}</div>
                <div class="label">Patients</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ $patients->sum('caisses_count') }}</div>
                <div class="label">Visites</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ number_format($patients->avg('caisses_count'), 1) }}</div>
                <div class="label">Moy. Visites</div>
            </div>
        </div>

        <!-- Tableau des patients -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Téléphone</th>
                    <th>Âge</th>
                    <th class="text-center">Nb Visites</th>
                    <th>Dernière Visite</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $index => $patient)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $patient->first_name }} {{ $patient->last_name }}</strong>
                    </td>
                    <td>{{ $patient->phone ?? 'N/A' }}</td>
                    <td>
                        @if($patient->date_naissance)
                            {{ \Carbon\Carbon::parse($patient->date_naissance)->age }} ans
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        <strong>{{ $patient->caisses_count }}</strong>
                    </td>
                    <td>
                        @if($patient->caisses->count() > 0)
                            @php
                                $lastCaisse = $patient->caisses->sortByDesc('created_at')->first();
                            @endphp
                            {{ $lastCaisse->created_at->format('d/m/Y à H:i') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Aucun patient trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pied de page -->
        <div class="footer">
            <p><strong>{{ config('clinique.name') }}</strong></p>
            <p>{{ config('clinique.website') }} - {{ config('clinique.phone') }}</p>
            <p>Document généré automatiquement le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>

