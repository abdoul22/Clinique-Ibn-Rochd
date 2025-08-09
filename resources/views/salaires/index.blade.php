@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Paie du mois {{ str_pad($month,2,'0',STR_PAD_LEFT) }}/{{ $year }}</h1>
    <div class="flex items-center gap-2">
        <a href="{{ route('salaires.pdf', ['year'=>$year,'month'=>$month]) }}" class="form-button">Télécharger PDF</a>
        <a href="{{ $url_paiement_un_par_un }}" class="form-button form-button-secondary">Payer un par un</a>
    </div>
</div>

<div class="card mb-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-gray-700 dark:text-gray-300">Année</label>
            <input type="number" name="year" value="{{ $year }}"
                class="form-input w-28 bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
        </div>
        <div>
            <label class="form-label text-gray-700 dark:text-gray-300">Mois</label>
            <input type="number" min="1" max="12" name="month" value="{{ $month }}"
                class="form-input w-24 bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
        </div>
        <button class="form-button">Filtrer</button>
    </form>
</div>

<div class="card mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
    <form method="POST" action="{{ route('salaires.payAll') }}" class="flex flex-wrap items-center gap-3">
        @csrf
        <div>
            <label class="form-label text-gray-700 dark:text-gray-300">Mode de paiement</label>
            <select name="mode"
                class="form-select bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
                @foreach($modes as $mode)
                <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
                @endforeach
            </select>
        </div>
        <button class="form-button bg-green-600 hover:bg-green-700">Payer tous les salaires</button>
    </form>
</div>

<div
    class="table-container bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
    <table class="table-main w-full">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
            <tr>
                <th>Personnel</th>
                <th>Fonction</th>
                <th>Salaire brut</th>
                <th>Crédit (Total Restant)</th>
                <th>Net à payer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-900 dark:text-gray-100">
            @foreach($personnels as $p)
            <tr
                class="{{ $p['is_paid'] ? 'bg-green-50 dark:bg-green-900/20' : ($p['credit_ce_mois']>0 ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-white dark:bg-gray-800') }} border-b border-gray-100 dark:border-gray-700">
                <td class="font-medium">{{ $p['nom'] }}</td>
                <td>{{ $p['fonction'] ?? '—' }}</td>
                <td>{{ number_format($p['salaire'], 0, ',', ' ') }} MRU</td>
                <td class="text-orange-600 dark:text-orange-400">{{ number_format($p['credit_restant'], 0, ',', ' ') }}
                    MRU</td>
                <td class="text-green-700 dark:text-green-400">{{ number_format($p['net_a_payer'], 0, ',', ' ') }} MRU
                </td>
                <td>
                    <form method="POST" action="{{ route('salaires.payOne', $p['id']) }}"
                        class="flex items-center gap-2">
                        @csrf
                        <select name="mode"
                            class="form-select bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600"
                            {{ $p['is_paid'] ? 'disabled' : '' }}>
                            @foreach($modes as $mode)
                            <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
                            @endforeach
                        </select>
                        <button
                            class="form-button bg-purple-600 hover:bg-purple-700 {{ $p['is_paid'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $p['is_paid'] ? 'disabled' : '' }}>
                            {{ $p['is_paid'] ? 'Payé' : 'Payer' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
