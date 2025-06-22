@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center">Créer un compte Admin</h2>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nom</label>
            <input type="text" name="name" placeholder="Votre nom complet" required
                class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" placeholder="exemple@mail.com" required
                class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input type="password" name="password" placeholder="••••••••" required
                class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" placeholder="••••••••" required
                class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </div>

        <button type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition duration-150">
            S’inscrire
        </button>
    </form>

    <p class="mt-4 text-center text-sm">
        Vous avez déjà un compte ?
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Se connecter</a>
    </p>
</div>
@endsection
