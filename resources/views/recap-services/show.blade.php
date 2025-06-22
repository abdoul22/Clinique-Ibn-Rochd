@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6">
    <h2 class="text-2xl font-semibold mb-4">Détails du récapitulatif</h2>
    <ul class="space-y-2 text-sm">
        <li><strong>ID :</strong> {{ $recap->id }}</li>
        <li><strong>Service :</strong> {{ $recap->service->nom ?? '—' }}</li>
        <li><strong>Total :</strong> {{ number_format($recap->total, 0, ',', ' ') }} MRU</li>
        <li><strong>Date :</strong> {{ $recap->date }}</li>
    </ul>
</div>
@endsection
