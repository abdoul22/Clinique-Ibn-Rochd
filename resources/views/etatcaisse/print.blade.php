<!-- resources/views/etatcaisse/print.blade.php -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Impression - État de caisse</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .period-info {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .summary {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
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
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .print-date {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">État de Caisse - Impression</div>
        @if(isset($periodDescription))
        <div class="period-info">{{ $periodDescription }}</div>
        @endif
        <div class="period-info">Imprimé le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</div>
    </div>

    @if(isset($resumeFiltre))
    <div class="summary">
        <div class="summary-title">Résumé de la période</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Recette Caisse</div>
                <div class="summary-value">{{ number_format($resumeFiltre['recette'], 0, ',', ' ') }} MRU</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Part Médecin</div>
                <div class="summary-value">{{ number_format($resumeFiltre['part_medecin'], 0, ',', ' ') }} MRU</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Part Clinique</div>
                <div class="summary-value">{{ number_format($resumeFiltre['part_cabinet'], 0, ',', ' ') }} MRU</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Dépense</div>
                <div class="summary-value">{{ number_format($resumeFiltre['depense'], 0, ',', ' ') }} MRU</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Crédit Personnel</div>
                <div class="summary-value">{{ number_format($resumeFiltre['credit_personnel'], 0, ',', ' ') }} MRU</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Crédit Assurance</div>
                <div class="summary-value">{{ number_format($resumeFiltre['credit_assurance'], 0, ',', ' ') }} MRU</div>
            </div>
        </div>
    </div>
    @endif

    @if($etatcaisses->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Recette</th>
                <th>Part Médecin</th>
                <th>Part Clinique</th>
                <th>Paiement</th>
                <th>Validation</th>
                <th>Assurance</th>
                <th>Médecin</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($etatcaisses as $etat)
            <tr>
                <td>{{ $etat->designation }}</td>
                <td class="text-right">{{ number_format($etat->recette, 0, ',', ' ') }} MRU</td>
                <td class="text-right">{{ number_format($etat->part_medecin, 0, ',', ' ') }} MRU</td>
                <td class="text-right">{{ number_format($etat->part_clinique, 0, ',', ' ') }} MRU</td>
                <td class="text-center">
                    @php $paiement = optional($etat->caisse)->paiements; @endphp
                    @if($paiement)
                    {{ $paiement->type }} ({{ number_format($paiement->montant, 0, ',', ' ') }} MRU)
                    @else
                    Assuré
                    @endif
                </td>
                <td class="text-center">
                    @if($etat->validated)
                    <span style="color: green;">✓ Validé</span>
                    @else
                    <span style="color: red;">✗ Non validé</span>
                    @endif
                </td>
                <td>{{ $etat->assurance?->nom ?? '—' }}</td>
                <td>{{ $etat->medecin?->nom_complet_avec_prenom ?? '—' }}</td>
                <td class="text-center">{{ $etat->created_at ? $etat->created_at->format('d/m/Y') : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 40px; color: #666;">
        <p>Aucune donnée trouvée pour la période sélectionnée.</p>
    </div>
    @endif

    <div class="print-date">
        Total des entrées: {{ $etatcaisses->count() }}
    </div>

    <!-- Boutons d'action (non imprimables) -->
    <div class="no-print" style="margin-top: 30px; text-align: center; padding: 20px;">
        <a href="{{ route('etatcaisse.index') }}" 
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
