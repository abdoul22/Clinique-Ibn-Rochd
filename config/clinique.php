<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Informations Générales de la Clinique
    |--------------------------------------------------------------------------
    |
    | Ces informations sont utilisées sur tous les documents imprimés (en-têtes,
    | pieds de page, reçus). Changez-les ici pour rebrander l'application.
    |
    */

    'name' => env('CLINIQUE_NAME', 'CLINIQUE CENTRE IBN ROCHD'), // Mis à jour selon l'image

    'address' => env('CLINIQUE_ADDRESS', 'Avenue John Kennedy, en face de la Polyclinique – Nouakchott'),

    'phone' => env('CLINIQUE_PHONE', 'Urgences Tél. 43 45 54 23 – 22 30 56 26'),

    'email' => env('CLINIQUE_EMAIL', 'contact@ibnrochd.pro'), // À confirmer si différent

    'website' => env('CLINIQUE_WEBSITE', 'ibnrochd.pro'),

    /*
    |--------------------------------------------------------------------------
    | Informations du Médecin Directeur
    |--------------------------------------------------------------------------
    */

    'director_name' => env('CLINIQUE_DIRECTOR_NAME', 'Dr Brahim Ould Ntaghry'),

    'director_specialty' => env('CLINIQUE_DIRECTOR_SPECIALTY', 'Spécialiste en Imagerie Médicale'),

    'center_type' => env('CLINIQUE_CENTER_TYPE', 'Centre Imagerie Médicale'),

    'services_description' => env('CLINIQUE_SERVICES_DESCRIPTION', 'Scanner – Echographie – Radiologie Générale – Mammographie – Panoramique Dentaire'),

    /*
    |--------------------------------------------------------------------------
    | Informations en Arabe
    |--------------------------------------------------------------------------
    */

    'name_ar' => env('CLINIQUE_NAME_AR', 'مركز ابن رشد'),

    'director_name_ar' => env('CLINIQUE_DIRECTOR_NAME_AR', 'الدكتور إبراهيم ولد نْتَغري'),

    'director_specialty_ar' => env('CLINIQUE_DIRECTOR_SPECIALTY_AR', 'اختصاصي في التشخيص الطبي والأشعة'),

    'center_type_ar' => env('CLINIQUE_CENTER_TYPE_AR', 'مركز التشخيص الطبي'),

    'services_description_ar' => env('CLINIQUE_SERVICES_DESCRIPTION_AR', 'فحص بالأشعة – تصوير بالموجات فوق الصوتية – أشعة عامة – تصوير الثدي – أشعة الأسنان البانورامية'),

    /*
    |--------------------------------------------------------------------------
    | Identité Visuelle
    |--------------------------------------------------------------------------
    */

    // Chemin relatif depuis le dossier public/ (ex: 'images/logo.png')
    'logo_path' => env('CLINIQUE_LOGO_PATH', 'images/logo.png'),

    // Couleur principale pour les titres et bordures (Hex)
    'primary_color' => env('CLINIQUE_PRIMARY_COLOR', '#1e40af'), // Bleu roi de l'en-tête

    /*
    |--------------------------------------------------------------------------
    | Configuration PWA (Progressive Web App)
    |--------------------------------------------------------------------------
    */

    // Nom court pour l'icône de l'application (max 12 caractères recommandé)
    'short_name' => env('CLINIQUE_SHORT_NAME', null), // Si null, sera généré automatiquement depuis 'name'

    // Couleur de fond pour le splash screen PWA
    'pwa_background_color' => env('CLINIQUE_PWA_BACKGROUND_COLOR', '#ffffff'),

    // Chemins des icônes PWA personnalisées (optionnel, utilise le logo par défaut)
    'pwa_icon_192' => env('CLINIQUE_PWA_ICON_192', null), // Ex: 'images/pwa-icon-192.png'
    'pwa_icon_512' => env('CLINIQUE_PWA_ICON_512', null), // Ex: 'images/pwa-icon-512.png'
];
