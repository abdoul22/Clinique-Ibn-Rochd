
   @extends('layouts.app')

   @section('content')

    <!-- Hero Section -->
    <section class="bg-indigo-600 text-white">
        <div class="max-w-7xl mx-auto px-4 py-20 text-center">
            <h1 class="text-4xl font-bold mb-4">Bienvenue dans le système de gestion <strong class="text-orange-200 ">Ibn Rochd</strong></h1>
            <br>
            <a href="{{ route('patients.index') }}"
                class="bg-white text-indigo-600 font-semibold px-6 py-3 rounded shadow hover:bg-gray-100 transition">
                Voir les patients
            </a>
        </div>
    </section>

    <!-- Features -->
    <section class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid md:grid-cols-3 gap-8 text-center">
            <div class="bg-white p-6 rounded shadow hover:shadow-lg transition">
                <h3 class="text-lg font-bold text-indigo-600 mb-2">Suivi des patients</h3>
                <p>Accédez rapidement aux informations médicales et administratives de chaque patient.</p>
            </div>
            <div class="bg-white p-6 rounded shadow hover:shadow-lg transition">
                <h3 class="text-lg font-bold text-indigo-600 mb-2">Gestion des rendez-vous</h3>
                <p>Planifiez et gérez les rendez-vous en toute simplicité.</p>
            </div>
            <div class="bg-white p-6 rounded shadow hover:shadow-lg transition">
                <h3 class="text-lg font-bold text-indigo-600 mb-2">Facturation claire</h3>
                <p>Générez des factures précises pour chaque patient en un seul clic.</p>
            </div>
        </div>
    </section>
@endsection
