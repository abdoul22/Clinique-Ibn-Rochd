@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <div>
            <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Réinitialiser le mot de passe
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
                Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>
        </div>
        @if (session('status'))
        <div
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 animate-fade-in dark:bg-green-900/20 dark:text-green-200 dark:border-green-800">
            {{ session('status') }}
        </div>
        @endif
        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                <input id="email" name="email" type="email" required autofocus
                    class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                    value="{{ old('email') }}">
                @error('email')
                <span class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 dark:bg-primary-700 hover:bg-primary-700 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                    Envoyer le lien de réinitialisation
                </button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 hover:underline">Retour à la
                connexion</a>
        </div>
    </div>
</div>
@endsection



