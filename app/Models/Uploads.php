<?php

/*
 * TomTroc — Model
 * - Encapsule l'accès à la base (PDO)
 * - Porte la logique métier (CRUD + requêtes)
 * - Utilisé par les contrôleurs
 */


namespace App\Models;

use RuntimeException;

class Uploads
{
    public static function storeImage(?array $file): ?string
    {
        // Image facultative
        if (
            $file === null ||
            !isset($file['error']) ||
            $file['error'] === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Erreur lors de l’envoi du fichier.');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException('Fichier trop volumineux (2 Mo max).');
        }

        // ✅ Vérification image SANS fileinfo
        $info = @getimagesize($file['tmp_name']);
        if ($info === false) {
            throw new RuntimeException('Le fichier envoyé n’est pas une image valide.');
        }

        $mime = $info['mime'];
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowed[$mime])) {
            throw new RuntimeException('Format d’image non autorisé.');
        }

        $dir = __DIR__ . '/../../public/uploads';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = uniqid('book_', true) . '.' . $allowed[$mime];
        $path = $dir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            throw new RuntimeException('Impossible de sauvegarder le fichier.');
        }

        // Chemin relatif stocké en DB
        return 'uploads/' . $filename;
    }
}
