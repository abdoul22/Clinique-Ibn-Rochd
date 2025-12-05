import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            outDir: 'public',
            buildBase: '/',
            scope: '/',
            includeAssets: ['favicon.ico', 'apple-touch-icon.png', 'mask-icon.svg'],
            manifest: false, // Désactiver la génération automatique du manifeste pour utiliser le contrôleur dynamique
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,jpg,jpeg}'],
                cleanupOutdatedCaches: true,
                navigateFallback: null, // Laravel gère le routing
            }
        })
    ],
    server: {
        cors: true,
    },
});
