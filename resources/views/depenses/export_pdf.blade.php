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
