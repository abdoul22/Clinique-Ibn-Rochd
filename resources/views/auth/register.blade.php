@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <div>
            <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Créer un compte
            </h2>
        </div>
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom
                        complet</label>
                    <input id="name" name="name" type="text" required autofocus
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                        value="{{ old('name') }}">
                    @error('name')
                    <span class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                    <input id="email" name="email" type="email" required
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                        value="{{ old('email') }}">
                    @error('email')
                    <span class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Mot de
                        passe</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm">
                    @error('password')
                    <span class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="password_confirmation"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200">Confirmer le mot de
                        passe</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        autocomplete="new-password"
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm">
                </div>
            </div>
            <div>
                <button type="submit" id="submitBtn"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 dark:bg-primary-700 hover:bg-primary-700 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition"
                    onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Inscription en cours...'; this.form.submit();">
                    S'inscrire
                </button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 hover:underline">Déjà inscrit ?
                Se connecter</a>
        </div>
    </div>
</div>
@endsection
