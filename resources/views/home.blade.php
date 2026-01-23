@extends('layouts.app')
@section('title', 'Accueil - ' . config('clinique.name'))

@section('content')
<style>
    /* Animations Douces & Premium */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }

    @keyframes float-delayed {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }

    @keyframes pulse-soft {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
        70% { box-shadow: 0 0 0 20px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }

    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-float-delayed { animation: float-delayed 7s ease-in-out infinite 1s; }
    .animate-fade-in-up { animation: fade-in-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }

    /* Glassmorphism & 3D Cards */
    .glass-card {
        background: rgba(255, 255, 255, 0.95); /* Plus opaque pour light mode */
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 1);
        box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.15); /* Ombre plus marquée */
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Dark Mode Support pour Glassmorphism - Renforcé */
    .dark .glass-card {
        background-color: rgba(17, 24, 39, 0.8) !important; /* Gray-900 + Opacity */
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.7) !important;
        color: #e5e7eb !important; /* text-gray-200 */
    }

    .dark .glass-card h3 {
        color: #ffffff !important;
    }

    .dark .glass-card p, 
    .dark .glass-card .text-gray-500 {
        color: #9ca3af !important; /* text-gray-400 */
    }

    .glass-card:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 30px 60px -15px rgba(59, 130, 246, 0.25);
        border-color: rgba(59, 130, 246, 0.4);
    }

    .dark .glass-card:hover {
        background-color: rgba(30, 41, 59, 0.9) !important; /* Slate-800 */
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.8) !important;
        border-color: rgba(59, 130, 246, 0.5) !important;
    }

    .icon-3d-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .icon-3d-wrapper::before {
        content: '';
        position: absolute;
        inset: -10px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        z-index: 0;
    }
    
    .text-gradient {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<!-- Hero Section Premium -->
<div class="relative min-h-[90vh] flex items-center overflow-hidden bg-slate-50 dark:bg-gray-900">
    <!-- Background Abstract Shapes -->
    <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-blue-100/40 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 animate-float"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-cyan-100/40 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4 animate-float-delayed"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <!-- Text Content -->
        <div class="text-left animate-fade-in-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 text-blue-700 dark:text-blue-300 font-semibold text-sm mb-6">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <span>Solution Hospitalière Intelligente</span>
            </div>
            
            <h1 class="text-5xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-6 leading-[1.1]">
                L'Excellence <br>
                <span class="text-gradient">Médicale</span> Digitalisée
            </h1>
            
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-lg leading-relaxed">
                Une plateforme unifiée pour une gestion clinique sans faille. Du dossier patient à la facturation, simplifiez chaque étape avec élégance et précision.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('patients.index') }}" class="group relative px-8 py-4 bg-blue-700 hover:bg-blue-600 text-white font-bold rounded-2xl shadow-xl shadow-blue-900/20 transition-all hover:shadow-blue-700/30 overflow-hidden">
                    <span class="relative z-10 flex items-center gap-3 text-lg">
                        Accéder au Portail
                        <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
                
                <a href="#features" class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-2xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all flex items-center justify-center gap-3 text-lg">
                    <i class="fas fa-layer-group text-blue-500"></i>
                    Découvrir les modules
                </a>
            </div>
        </div>

        <!-- 3D Illustration Area -->
        <div class="relative h-[500px] hidden lg:flex items-center justify-center perspective-1000 animate-fade-in-up delay-200">
            <!-- Central Card (Patient) -->
            <div class="glass-card absolute z-20 w-80 p-6 rounded-3xl top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 animate-float">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-user-injured text-xl"></i>
                    </div>
                    <div>
                        <div class="h-3 w-24 bg-gray-200 rounded-full mb-2"></div>
                        <div class="h-2 w-16 bg-gray-100 rounded-full"></div>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="h-10 w-full bg-blue-50 rounded-xl flex items-center px-3">
                        <div class="h-2 w-1/3 bg-blue-200 rounded-full"></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="h-16 w-1/2 bg-gray-50 rounded-xl"></div>
                        <div class="h-16 w-1/2 bg-gray-50 rounded-xl"></div>
                    </div>
                </div>
            </div>

            <!-- Floating Card 1 (Stats) -->
            <div class="glass-card absolute z-10 w-64 p-5 rounded-3xl top-20 right-10 animate-float-delayed">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400">Activité</span>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full dark:bg-green-900 dark:text-green-300">+12%</span>
                </div>
                <div class="h-24 flex items-end justify-between gap-1">
                    <div class="w-full bg-blue-100 dark:bg-blue-900/40 rounded-t-sm h-[40%]"></div>
                    <div class="w-full bg-blue-200 dark:bg-blue-800/50 rounded-t-sm h-[60%]"></div>
                    <div class="w-full bg-blue-500 rounded-t-sm h-[80%] shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                    <div class="w-full bg-blue-300 dark:bg-blue-700/60 rounded-t-sm h-[50%]"></div>
                    <div class="w-full bg-blue-100 dark:bg-blue-900/40 rounded-t-sm h-[30%]"></div>
                </div>
            </div>

            <!-- Floating Card 2 (Secure) -->
            <div class="glass-card absolute z-10 w-56 p-4 rounded-3xl bottom-20 left-10 animate-float" style="animation-delay: -2s;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200">Données Sécurisées</div>
                        <div class="text-xs text-green-600 dark:text-green-400">Chiffrement AES-256</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Grid -->
<section id="features" class="py-24 bg-white dark:bg-gray-900 relative overflow-hidden transition-colors duration-300">
    <div class="container mx-auto px-4 sm:px-6">
        <div class="text-center max-w-3xl mx-auto mb-20 animate-fade-in-up delay-100">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">L'écosystème complet</h2>
            <p class="text-lg text-gray-500 dark:text-gray-400">Tout ce dont votre clinique a besoin, centralisé dans une interface unique et intuitive.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="glass-card p-8 rounded-3xl group animate-fade-in-up delay-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Dossier Médical</h3>
                <p class="leading-relaxed">Historique complet, consultations, ordonnances et imagerie. Tout le parcours patient en un coup d'œil.</p>
            </div>

            <!-- Card 2 -->
            <div class="glass-card p-8 rounded-3xl group animate-fade-in-up delay-200">
                <div class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Facturation & Caisse</h3>
                <p class="leading-relaxed">Gestion fluide des paiements, devis, et remboursements assurances. Une traçabilité financière totale.</p>
            </div>

            <!-- Card 3 -->
            <div class="glass-card p-8 rounded-3xl group animate-fade-in-up delay-300">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-flask"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Laboratoire</h3>
                <p class="leading-relaxed">Demandes d'examens et résultats intégrés directement au dossier patient. Zéro papier, zéro perte.</p>
            </div>

             <!-- Card 4 -->
             <div class="glass-card p-8 rounded-3xl group animate-fade-in-up delay-100">
                <div class="w-14 h-14 rounded-2xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-procedures"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Hospitalisation</h3>
                <p class="leading-relaxed">Gestion des lits, admissions et sorties. Vue en temps réel de l'occupation des services.</p>
            </div>

             <!-- Card 5 -->
             <div class="glass-card p-8 rounded-3xl group animate-fade-in-up delay-200">
                <div class="w-14 h-14 rounded-2xl bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Rapports & Stats</h3>
                <p class="leading-relaxed">Tableaux de bord décisionnels pour suivre la performance de votre clinique au jour le jour.</p>
            </div>

             <!-- Card 6 -->
             <div class="glass-card p-8 rounded-3xl group animate-fade-in-up delay-300">
                <div class="w-14 h-14 rounded-2xl bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Admin & Sécurité</h3>
                <p class="leading-relaxed">Gestion fine des rôles (Médecin, Caissier, Admin) et traçabilité complète des actions.</p>
            </div>
        </div>
    </div>
</section>


@endsection
