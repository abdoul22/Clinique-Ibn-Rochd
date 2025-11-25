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

    'name' => env('CLINIQUE_NAME', 'CENTRE IBN ROCHD'), // Mis à jour selon l'image

    'address' => env('CLINIQUE_ADDRESS', 'Avenue John Kennedy, en face de la Polyclinique – Nouakchott'),

    'phone' => env('CLINIQUE_PHONE', 'Urgences Tél. 43 45 54 23 – 22 30 56 26'),

    'email' => env('CLINIQUE_EMAIL', 'contact@clinique.com'), // À confirmer si différent

    'website' => env('CLINIQUE_WEBSITE', 'ibnrochd.pro'),

    /*
    |--------------------------------------------------------------------------
    | Identité Visuelle
    |--------------------------------------------------------------------------
    */

    // Chemin relatif depuis le dossier public/ (ex: 'images/logo.png')
    'logo_path' => env('CLINIQUE_LOGO_PATH', 'images/logo.png'),

    // Couleur principale pour les titres et bordures (Hex)
    'primary_color' => env('CLINIQUE_PRIMARY_COLOR', '#1e40af'), // Bleu roi de l'en-tête
];
