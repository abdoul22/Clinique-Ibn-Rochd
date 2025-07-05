@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Détails du Patient</h2>
        <a href="{{ route('patients.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">←
            Retour à la liste</a>
    </div>

    <div class="space-y-4 text-gray-900 dark:text-gray-100">
        <p><strong>Nom :</strong> {{ $patient->first_name }} {{ $patient->last_name }}</p>
        <p><strong>Sexe :</strong> {{ $patient->gender }}</p>
        <p><strong>Date de naissance :</strong> {{ $patient->date_of_birth }}</p>
        <p><strong>Téléphone :</strong> {{ $patient->phone }}</p>
        <p><strong>Adresse :</strong> {{ $patient->address }}</p>
    </div>
</div>
@endsection
