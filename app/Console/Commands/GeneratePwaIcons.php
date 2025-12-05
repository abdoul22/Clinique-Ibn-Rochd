<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GeneratePwaIcons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pwa:generate-icons {--force : Force la régénération même si les icônes existent déjà}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les icônes PWA (192x192 et 512x512) à partir du logo de la clinique configuré';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = config('clinique');
        $logoPath = $config['logo_path'] ?? 'images/logo.png';
        $fullLogoPath = public_path($logoPath);

        if (!file_exists($fullLogoPath)) {
            $this->error("Le logo de la clinique n'existe pas : {$fullLogoPath}");
            $this->info("Veuillez placer votre logo dans : public/{$logoPath}");
            return Command::FAILURE;
        }

        // Vérifier si GD est disponible
        if (!extension_loaded('gd')) {
            $this->error("L'extension GD n'est pas disponible. Veuillez l'installer pour générer les icônes.");
            return Command::FAILURE;
        }

        $this->info("Génération des icônes PWA à partir de : {$logoPath}");

        // Générer l'icône 192x192
        $icon192Path = public_path('pwa-192x192.png');
        if (!$this->option('force') && file_exists($icon192Path)) {
            $this->warn("L'icône 192x192 existe déjà. Utilisez --force pour la régénérer.");
        } else {
            if ($this->resizeImage($fullLogoPath, $icon192Path, 192, 192)) {
                $this->info("✓ Icône 192x192 générée : pwa-192x192.png");
            } else {
                $this->error("✗ Erreur lors de la génération de l'icône 192x192");
                return Command::FAILURE;
            }
        }

        // Générer l'icône 512x512
        $icon512Path = public_path('pwa-512x512.png');
        if (!$this->option('force') && file_exists($icon512Path)) {
            $this->warn("L'icône 512x512 existe déjà. Utilisez --force pour la régénérer.");
        } else {
            if ($this->resizeImage($fullLogoPath, $icon512Path, 512, 512)) {
                $this->info("✓ Icône 512x512 générée : pwa-512x512.png");
            } else {
                $this->error("✗ Erreur lors de la génération de l'icône 512x512");
                return Command::FAILURE;
            }
        }

        $this->info("\n✅ Les icônes PWA ont été générées avec succès !");
        $this->info("Les icônes seront automatiquement utilisées par le manifest PWA dynamique.");

        return Command::SUCCESS;
    }

    /**
     * Redimensionne une image à la taille spécifiée
     */
    private function resizeImage($sourcePath, $destinationPath, $width, $height)
    {
        // Détecter le type d'image
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $mimeType = $imageInfo['mime'];
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];

        // Créer l'image source selon le type
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($sourcePath);
                break;
            default:
                $this->error("Type d'image non supporté : {$mimeType}");
                return false;
        }

        if (!$sourceImage) {
            return false;
        }

        // Créer une nouvelle image avec fond transparent pour PNG
        $destinationImage = imagecreatetruecolor($width, $height);
        
        // Préserver la transparence pour PNG
        if ($mimeType === 'image/png') {
            imagealphablending($destinationImage, false);
            imagesavealpha($destinationImage, true);
            $transparent = imagecolorallocatealpha($destinationImage, 255, 255, 255, 127);
            imagefill($destinationImage, 0, 0, $transparent);
        } else {
            // Fond blanc pour les autres formats
            $white = imagecolorallocate($destinationImage, 255, 255, 255);
            imagefill($destinationImage, 0, 0, $white);
        }

        // Calculer les dimensions pour conserver les proportions
        $ratio = min($width / $sourceWidth, $height / $sourceHeight);
        $newWidth = (int)($sourceWidth * $ratio);
        $newHeight = (int)($sourceHeight * $ratio);
        
        // Centrer l'image
        $x = (int)(($width - $newWidth) / 2);
        $y = (int)(($height - $newHeight) / 2);

        // Redimensionner avec une meilleure qualité
        imagecopyresampled(
            $destinationImage,
            $sourceImage,
            $x, $y, 0, 0,
            $newWidth, $newHeight,
            $sourceWidth, $sourceHeight
        );

        // Sauvegarder l'image
        $result = imagepng($destinationImage, $destinationPath, 9); // Qualité maximale pour PNG

        // Libérer la mémoire
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);

        return $result;
    }
}
