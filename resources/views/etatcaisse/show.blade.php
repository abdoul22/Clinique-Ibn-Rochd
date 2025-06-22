<div class="bg-white shadow p-6 rounded">
    <h2 class="text-xl font-semibold mb-4">Détails de l'État de Caisse</h2>
    <ul class="space-y-2">
        <li><strong>Désignation :</strong> {{ $etatcaisse->designation }}</li>
        <li><strong>Recette :</strong> {{ number_format($etatcaisse->recette, 2) }} MRU</li>
        <li><strong>Part Médecin :</strong> {{ number_format($etatcaisse->part_medecin, 2) }} MRU</li>
        <li><strong>Part Clinique :</strong> {{ number_format($etatcaisse->part_clinique, 2) }} MRU</li>
        <li><strong>Dépense :</strong> {{ number_format($etatcaisse->depense, 2) }} MRU</li>
        <li><strong>Crédit Personnel :</strong>
            {{ $etatcaisse->personnel ? $etatcaisse->personnel->nom . ' (' .
            number_format($etatcaisse->credit_personnel, 2) . ' MRU)' : 'N/A' }}
        </li>
        <li><strong>Assurance :</strong>
            {{ $etatcaisse->assurance ? $etatcaisse->assurance->nom : 'N/A' }}
        </li>
    </ul>
</div>
