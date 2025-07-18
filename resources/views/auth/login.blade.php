@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 py-8 px-2 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-4 sm:p-8">
        <div>
            <h2 class="mt-2 text-center text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white">
                Connexion à votre compte
            </h2>
        </div>
        @if(session('error'))
        <div
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate-fade-in text-base">
            {{ session('error') }}
        </div>
        @endif
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label for="email"
                        class="block text-base font-medium text-gray-700 dark:text-gray-200">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required autofocus
                        class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 text-base sm:text-lg"
                        value="{{ old('email') }}">
                    @error('email')
                    <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-base font-medium text-gray-700 dark:text-gray-200">Mot de
                        passe</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 text-base sm:text-lg">
                    @error('password')
                    <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-between mb-4 gap-2">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox"
                        class="h-5 w-5 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-700 rounded dark:bg-gray-900">
                    <label for="remember_me" class="ml-2 block text-base text-gray-900 dark:text-gray-200">
                        Se souvenir de moi
                    </label>
                </div>
                <div class="text-base">
                    <a href="{{ route('password.request') }}"
                        class="font-medium text-primary-600 dark:text-primary-400 hover:underline">Mot de passe oublié
                        ?</a>
                </div>
            </div>
            <div>
                <button type="submit" id="submitBtn"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-md text-white bg-primary-600 dark:bg-primary-700 hover:bg-primary-700 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition"
                    onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Connexion en cours...'; this.form.submit();">
                    Se connecter
                </button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('register') }}"
                class="text-primary-600 dark:text-primary-400 hover:underline text-base">Créer un compte</a>
        </div>
    </div>
</div>
@endsection
