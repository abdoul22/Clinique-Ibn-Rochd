<table class="table-main border mb-6">
    <thead class="bg-gray-100 dark:bg-gray-700">
        <tr>
            <th class="table-header">#</th>
            <th class="table-header">Nom</th>
            <th class="table-header">Montant</th>
            <th class="table-header">Mode de paiement</th>
            <th class="table-header">Statut</th>
            <th class="table-header">Action</th>
        </tr>
    </thead>
    <tbody class="table-body">
        @forelse($credits as $credit)
        <tr class="table-row odd:bg-gray-50 dark:odd:bg-gray-800">
            <td class="table-cell">{{ $credit->id }}</td>
            <td class="table-cell">
                {{ $credit->source?->nom ?? '---' }}
                @if($credit->source_type === 'App\\Models\\Personnel')
                <span
                    class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded ml-2">Personnel</span>
                @elseif($credit->source_type === 'App\\Models\\Assurance')
                <span
                    class="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded ml-2">Assurance</span>
                @endif
            </td>
            <td class="table-cell">{{ number_format($credit->montant, 0, ',', ' ') }} MRU</td>
            <td class="table-cell">
                @if($credit->source_type === 'App\\Models\\Personnel')
                <span class="text-blue-600 dark:text-blue-400 font-medium">Déduction salariale</span>
                @else
                {{ ucfirst($credit->mode_paiement_id ?? '---') }}
                @endif
            </td>
            <td class="table-cell">
                @if($credit->status === 'non payé')
                <span class="text-red-500 dark:text-red-400 font-semibold">Non payé</span>
                @elseif($credit->status === 'partiellement payé')
                <span class="text-yellow-500 dark:text-yellow-400 font-semibold">Partiellement payé</span>
                @else
                <span class="text-green-600 dark:text-green-400 font-semibold">Payé</span>
                @endif
            </td>
            <td class="table-cell">
                @if($credit->status !== 'payé')
                @if($credit->source_type === 'App\\Models\\Personnel')
                <a href="{{ route('credits.payer', $credit->id) }}"
                    class="form-button text-sm bg-blue-600 hover:bg-blue-700">Payer par salaire</a>
                @else
                <a href="{{ route('credits.payer', $credit->id) }}" class="form-button text-sm">Payer</a>
                @endif
                @else
                ---
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="table-cell text-center text-gray-500 dark:text-gray-400 py-4">Aucun crédit trouvé.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
