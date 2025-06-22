@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Détails du Patient</h2>
        <a href="{{ route('patients.index') }}" class="text-sm text-blue-600 hover:underline">← Retour à la liste</a>
    </div>

    <div class="space-y-4">
        <p><strong>Nom :</strong> {{ $patient->first_name }} {{ $patient->last_name }}</p>
        <p><strong>Sexe :</strong> {{ $patient->gender }}</p>
        <p><strong>Date de naissance :</strong> {{ $patient->date_of_birth }}</p>
        <p><strong>Téléphone :</strong> {{ $patient->phone }}</p>
        <p><strong>Adresse :</strong> {{ $patient->address }}</p>
        <p><strong>Type de patient :</strong> {{ $patient->type_patient }}</p>
    </div>
</div>
@endsection
