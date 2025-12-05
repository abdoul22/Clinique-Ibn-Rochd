<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function __invoke(Request $request)
    {
        $config = config('clinique');

        // Générer le nom court à partir du nom complet (premiers mots)
        $shortName = $config['short_name'] ?? substr($config['name'] ?? 'Clinique', 0, 12);

        // Utiliser les icônes PWA personnalisées si configurées, sinon les icônes par défaut
        // Ne pas utiliser directement le logo car il peut ne pas avoir les bonnes dimensions
        $icon192Path = $config['pwa_icon_192'] ?? 'pwa-192x192.png';
        $icon512Path = $config['pwa_icon_512'] ?? 'pwa-512x512.png';

        // Vérifier que les fichiers existent, sinon utiliser les icônes par défaut
        if (!file_exists(public_path($icon192Path))) {
            $icon192Path = 'pwa-192x192.png';
        }
        if (!file_exists(public_path($icon512Path))) {
            $icon512Path = 'pwa-512x512.png';
        }

        // Déterminer le chemin de base (pour les installations dans un sous-dossier)
        $baseUrl = $request->getSchemeAndHttpHost();
        $basePath = $request->getBasePath(); // Récupère le chemin de base (ex: /ibnrochd/public)

        // Nettoyer le basePath pour enlever index.php s'il est présent
        $basePath = str_replace('/index.php', '', $basePath);
        $basePath = rtrim($basePath, '/'); // Enlever le slash final s'il existe

        $startUrl = $basePath ? $basePath . '/' : '/';
        $scope = $basePath ? $basePath . '/' : '/';

        // Construire les URLs des icônes directement (sans passer par le routing Laravel)
        // Cela évite d'ajouter index.php dans l'URL
        $icon192 = $baseUrl . ($basePath ? $basePath . '/' : '/') . ltrim($icon192Path, '/');
        $icon512 = $baseUrl . ($basePath ? $basePath . '/' : '/') . ltrim($icon512Path, '/');

        $manifest = [
            'name' => $config['name'] ?? 'Clinique Ibn Rochd',
            'short_name' => $shortName,
            'description' => $config['services_description'] ?? 'Gestion de Clinique',
            'theme_color' => $config['primary_color'] ?? '#1e40af',
            'background_color' => $config['pwa_background_color'] ?? '#ffffff',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'start_url' => $startUrl,
            'scope' => $scope,
            'icons' => [
                [
                    'src' => $icon192,
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => $icon512,
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ],
                [
                    'src' => $icon512,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ]
            ]
        ];

        return response()->json($manifest)
            ->withHeaders([
                'Content-Type' => 'application/manifest+json',
                'Cache-Control' => 'public, max-age=3600', // Cache 1 heure
            ]);
    }
}
