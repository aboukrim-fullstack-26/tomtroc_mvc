<?php
/**
 * config/config.sample.php
 *
 * Rôle :
 * - Point d’entrée / composant du MVC TomTroc.
 * - Commentaires ajoutés pour faciliter debug & évolutions (V4 stable).
 *
 * Ordre d’exécution (général) :
 * public/index.php → app/bootstrap.php → Router → Controller → Model(s) → View(s)
 *
 * @author aboukrim
 * @date 2026-02-10
 */
// Copiez ce fichier en config.php et renseignez vos informations.
// IMPORTANT: ne versionnez pas config.php (déjà dans .gitignore)

return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'tomtroc',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => '', // ex: '' en local, ou '/tomtroc' si sous-dossier
        'uploads' => [
            'avatars_dir' => __DIR__ . '/../public/uploads/avatars',
            'books_dir'   => __DIR__ . '/../public/uploads/books',
            'max_bytes'   => 3 * 1024 * 1024,
            'allowed_mimes' => ['image/jpeg','image/png','image/webp'],
        ],
    ],
];
