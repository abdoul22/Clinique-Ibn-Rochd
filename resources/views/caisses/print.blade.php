@extends('layouts.app')
@section('content')
<div class="bg-white p-4">
    <h2 class="text-xl font-bold mb-4">Impression des caisses</h2>
    <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b">
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Patient</th>
                <th class="px-4 py-2">Examinateur</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caisses as $caisse)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $caisse->id }}</td>
                <td class="px-4 py-2">{{ $caisse->patient->nom ?? '—' }}</td>
                <td class="px-4 py-2">{{ $caisse->medecin->nom ?? '—' }}</td>
                <td class="px-4 py-2">{{ $caisse->date_examen }}</td>
                <td class="px-4 py-2">{{ number_format($caisse->total, 0, ',', ' ') }} MRU</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
