@extends('layouts.app')

@section('content')

<div class="grid grid-cols-1 bg-neutral-200 p-4 md:grid-cols-3 gap-4">
    <div class="bg-neutral-500 font-bold text-amber-100 shadow p-4 rounded ">
        <h3 class="font-semibold">Prix total des parts</h3>
        <p>{{ number_format($totaux['part_medecin_total'], 2) }} MRU</p>
    </div>
    <div class="shadow p-4 rounded bg-neutral-500 font-bold text-amber-100">
        <h3 class="font-semibold">Total Part cabinet</h3>
        <p>{{ number_format($totaux['part_cabinet_total'], 2) }} MRU</p>
    </div>
    <div class="bg-neutral-500 font-bold text-amber-100 shadow p-4 rounded">
        <h3 class="font-semibold">Total Part médecin</h3>
        <p>{{ number_format($totaux['part_medecin_total'], 2) }} MRU</p>
    </div>
    <div class="bg-neutral-500 font-bold text-amber-100 shadow p-4 rounded">
        <h3 class="font-semibold">Total Recettes (Factures)</h3>
        <p>{{ number_format($totaux['recettes_total'], 2) }} MRU</p>
    </div>
    <div class="bg-neutral-500 font-bold text-amber-100 shadow p-4 rounded">
        <h3 class="font-semibold">Total Dépenses</h3>
        <p>{{ number_format($totaux['depenses_total'], 2) }} MRU</p>
    </div>

    
</div>
@endsection
