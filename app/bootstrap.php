<?php

// 1) Charger la config (chemin absolu)
$configPath = __DIR__ . '/../config/config.php';

if (!file_exists($configPath)) {
    die("Fichier config introuvable : " . $configPath);
}

require_once $configPath;

// 2) Vérification immédiate
if (!defined('DB_NAME') || DB_NAME === '') {
    die("Config chargée mais DB_NAME est manquant. Vérifie config/config.php");
}

// 3) Session (avant toute sortie)
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
