<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function __invoke()
    {
        $config = config('clinique');

        // Générer le nom court à partir du nom complet (premiers mots)
        $shortName = $config['short_name'] ?? substr($config['name'] ?? 'Clinique', 0, 12);

        // Utiliser le logo de la clinique comme icône si disponible, sinon utiliser les icônes PWA par défaut
        $logoPath = $config['logo_path'] ?? 'images/logo.png';
        $logoExists = file_exists(public_path($logoPath));

        // Déterminer les chemins des icônes
        $icon192 = $config['pwa_icon_192'] ?? ($logoExists ? $logoPath : 'pwa-192x192.png');
        $icon512 = $config['pwa_icon_512'] ?? ($logoExists ? $logoPath : 'pwa-512x512.png');

        $manifest = [
            'name' => $config['name'] ?? 'Clinique Ibn Rochd',
            'short_name' => $shortName,
            'description' => $config['services_description'] ?? 'Gestion de Clinique',
            'theme_color' => $config['primary_color'] ?? '#1e40af',
            'background_color' => $config['pwa_background_color'] ?? '#ffffff',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'start_url' => '/',
            'scope' => '/',
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

        return response()->json($manifest)->withHeaders([
            'Content-Type' => 'application/manifest+json',
        ]);
    }
}
