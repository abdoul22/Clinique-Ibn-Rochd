<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Impression - Récapitulatif des opérateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
        }

        .resume-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }

        .resume-table th,
        .resume-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        .resume-table th {
            background: #e0e7ff;
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
    <h2>Récapitulatif des opérateurs</h2>
    @if(!empty($periodSummary))
    <div style="margin-bottom: 10px; color: #374151; font-weight: bold;">{{ $periodSummary }}</div>
    @endif
    @if($resume)
    <table class="resume-table">
        <tr>
            <th>Total Examens</th>
            <th>Total Recettes</th>
            <th>Part Médecin</th>
            <th>Part Clinique</th>
        </tr>
        <tr>
            <td>{{ number_format($resume['total_examens'], 0, ',', ' ') }}</td>
            <td>{{ number_format($resume['total_recettes'], 0, ',', ' ') }} MRU</td>
            <td>{{ number_format($resume['total_part_medecin'], 0, ',', ' ') }} MRU</td>
            <td>{{ number_format($resume['total_part_clinique'], 0, ',', ' ') }} MRU</td>
        </tr>
    </table>
    @endif
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Médecin</th>
                <th>Examen</th>
                <th>Nombre</th>
                <th>Tarif</th>
                <th>Recettes</th>
                <th>Part médecin</th>
                <th>Part clinique</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recapOperateurs as $recap)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $recap->medecin->nom ?? '—' }}</td>
                <td>{{ $recap->examen->nom ?? '—' }}</td>
                <td>{{ $recap->nombre }}</td>
                <td>{{ number_format($recap->tarif, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->recettes, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->part_medecin, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->part_clinique, 0, ',', ' ') }}</td>
                <td>{{ \Carbon\Carbon::parse($recap->jour)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="no-print" style="margin-top: 20px; text-align: center; padding: 20px;">
        <a href="{{ route(auth()->user()->role->name . '.recap-operateurs.index') }}" 
           style="display: inline-block; background: #6b7280; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-size: 16px; margin-right: 10px; transition: background 0.3s;">
            ← Retour
        </a>
        <button onclick="window.print()"
            style="background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px; transition: background 0.3s;">
            🖨️ Imprimer
        </button>
    </div>
</body>

</html>
