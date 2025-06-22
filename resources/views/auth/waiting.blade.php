@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow text-center">
    <h2 class="text-xl font-bold mb-4 text-red-600">Compte en attente</h2>
    <p>Votre compte est en attente d'approbation par un administrateur.</p>
    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">
            Se déconnecter
        </button>
    </form>
</div>
@endsection
