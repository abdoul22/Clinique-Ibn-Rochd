@extends('layouts.app')
@section('title', 'Accueil - Clinique Ibn Rochd')
@section('content')
<!-- Hero Section -->
<section
    class="hero-section text-white relative overflow-hidden bg-gradient-to-b from-primary-700 to-primary-900 dark:from-gray-900 dark:to-gray-950 min-h-[350px] flex items-center justify-center py-12 sm:py-20 md:py-28">
    <div class="max-w-3xl mx-auto px-2 sm:px-4 text-center relative z-10 animate-fade-in">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-6 leading-tight drop-shadow-lg">
            Système de gestion médicale<br>
            <span class="font-extrabold text-4xl sm:text-5xl md:text-6xl">Clinique </span>
            <span class="text-secondary-200 font-extrabold text-4xl sm:text-5xl md:text-6xl">Ibn Rochd</span>
        </h1>
        <a href="{{ route('patients.index') }}"
            class="inline-block bg-white dark:bg-gray-800 text-primary-600 dark:text-white font-bold px-8 py-4 sm:px-10 sm:py-5 rounded-full shadow-xl hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-lg sm:text-xl mt-4">
            <i class="fas fa-users mr-2"></i>Voir les patients
        </a>
    </div>
    <!-- Waves divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
            class="fill-current text-white dark:text-gray-900">
            <path
                d="M0 0v46.29c47.79 22.2 103.59 32.17 158 28 70.36-5.37 136.33-33.31 206.8-37.5 73.84-4.36 147.54 16.88 218.2 35.26 69.27 18 138.3 24.88 209.4 13.08 36.15-6 69.85-17.84 104.45-29.34C989.49 25 1113-14.29 1200 52.47V0z"
                opacity=".25" />
            <path
                d="M0 0v15.81c13 21.11 27.64 41.05 47.69 56.24C99.41 111.27 165 111 224.58 91.58c31.15-10.15 60.09-26.07 89.67-39.8 40.92-19 84.73-46 130.83-49.67 36.26-2.85 70.9 9.42 98.6 31.56 31.77 25.39 62.32 62 103.63 73 40.44 10.79 81.35-6.69 119.13-24.28s75.16-39 116.92-43.05c59.73-5.85 113.28 22.88 168.9 38.84 30.2 8.66 59 6.17 87.09-7.5 22.43-10.89 48-26.93 60.65-49.24V0z"
                opacity=".5" />
            <path
                d="M0 0v5.63C149.93 59 314.09 71.32 475.83 42.57c43-7.64 84.23-20.12 127.61-26.46 59-8.63 112.48 12.24 165.56 35.4C827.93 77.22 886 95.24 951.2 90c86.53-7 172.46-45.71 248.8-84.81V0z" />
        </svg>
    </div>
</section>
<!-- Features -->
<section class="max-w-7xl mx-auto px-2 sm:px-4 py-10 sm:py-16">
    <div class="text-center mb-10 sm:mb-16">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-primary-700 dark:text-white mb-4">
            Gestion médicale simplifiée
        </h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
        <!-- Feature 1 -->
        <div
            class="card-hover bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-xl flex flex-col items-center text-center">
            <div class="feature-icon bg-blue-50 dark:bg-blue-900/20 mb-4">
                <i class="fas fa-user-injured text-primary-600 dark:text-primary-300 text-4xl"></i>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Suivi des patients</h3>
            <p class="text-gray-600 dark:text-gray-300 text-base sm:text-lg">
                Accédez rapidement aux informations médicales et administratives de chaque patient grâce à une interface
                intuitive.
            </p>
        </div>
        <!-- Feature 2 -->
        <div
            class="card-hover bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-xl flex flex-col items-center text-center">
            <div class="feature-icon bg-orange-50 dark:bg-orange-900/20 mb-4">
                <i class="fas fa-calendar-check text-orange-500 dark:text-orange-300 text-4xl"></i>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Gestion des rendez-vous</h3>
            <p class="text-gray-600 dark:text-gray-300 text-base sm:text-lg">
                Planifiez et gérez les rendez-vous en toute simplicité avec notre système de calendrier intelligent.
            </p>
        </div>
        <!-- Feature 3 -->
        <div
            class="card-hover bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-xl flex flex-col items-center text-center">
            <div class="feature-icon bg-green-50 dark:bg-green-900/20 mb-4">
                <i class="fas fa-file-invoice-dollar text-green-500 dark:text-green-300 text-4xl"></i>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Facturation claire</h3>
            <p class="text-gray-600 dark:text-gray-300 text-base sm:text-lg">
                Générez des factures précises pour chaque patient en un seul clic et simplifiez votre gestion
                financière.
            </p>
        </div>
    </div>
</section>
<!-- CTA Section -->
<section
    class="bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-900 dark:to-primary-800 text-white py-10 sm:py-16">
    <div class="max-w-4xl mx-auto px-2 sm:px-4 text-center">
        <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold mb-4 text-white">Prêt à transformer votre gestion
            médicale ?</h2>
        <p class="text-indigo-100 dark:text-indigo-300 text-base sm:text-xl mb-6 max-w-2xl mx-auto">
            Rejoignez des centaines de professionnels de santé qui utilisent déjà Ibn Rochd
        </p>
        <a href="{{ route('patients.index') }}"
            class="inline-block bg-white dark:bg-gray-800 text-primary-700 dark:text-white font-bold px-8 py-4 sm:px-10 sm:py-5 rounded-full shadow-xl hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-lg sm:text-xl">
            <i class="fas fa-play-circle mr-2"></i>Commencer maintenant
        </a>
    </div>
</section>
@endsection
