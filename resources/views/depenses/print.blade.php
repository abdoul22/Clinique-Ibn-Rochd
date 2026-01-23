<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Impression Dépenses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
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

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center; padding: 20px;">
        <a href="{{ route('depenses.index', request()->query()) }}" 
           style="display: inline-block; background: #6b7280; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-size: 16px; margin-right: 10px; transition: background 0.3s;">
            ← Retour
        </a>
        <button onclick="window.print()"
            style="background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px; transition: background 0.3s;">
            🖨️ Imprimer
        </button>
    </div>

    <h2>Liste des dépenses</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Montant (MRU)</th>
                <th>Mode de paiement</th>
                <th>Source</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($depenses as $depense)
            <tr>
                <td>{{ $depense->id }}</td>
                <td>{{ $depense->nom }}</td>
                <td>{{ number_format($depense->montant, 0, ',', ' ') }}</td>
                <td>
                    @if($depense->mode_paiement_id === 'salaire')
                        Déduction salariale
                    @else
                        {{ ucfirst($depense->mode_paiement_id ?? 'Non défini') }}
                    @endif
                </td>
                <td>
                    @if($depense->mode_paiement_id === 'salaire')
                        Déduction salariale
                    @elseif(str_contains($depense->nom, 'Part médecin'))
                        Part médecin
                    @elseif($depense->source === 'automatique')
                        Généré automatiquement
                    @else
                        {{ ucfirst($depense->source ?? 'Manuelle') }}
                    @endif
                </td>
                <td>{{ $depense->created_at ? $depense->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <th>{{ number_format($depenses->sum('montant'), 0, ',', ' ') }} MRU</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
