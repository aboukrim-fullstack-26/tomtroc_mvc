<?php
/**
 * Database (PDO singleton + gestion erreurs)
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

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    /**
     * Méthode : cfg()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    private static function cfg(string $key, mixed $default = null): mixed
    {
        return \defined($key) ? \constant($key) : $default;
    }

    /**
     * Méthode : get()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public static function get(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = (string) self::cfg('DB_HOST', '127.0.0.1');
        $port = (int) self::cfg('DB_PORT', 3306);
        $name = (string) self::cfg('DB_NAME', '');
        $user = (string) self::cfg('DB_USER', 'root');
        $pass = (string) self::cfg('DB_PASS', '');
        $charset = (string) self::cfg('DB_CHARSET', 'utf8mb4');

        if ($name === '') {
            die("DB_NAME est vide ou non chargé. Vérifie config/config.php et app/bootstrap.php");
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return self::$pdo;
        } catch (PDOException $e) {
            $msg = "Erreur DB: " . $e->getMessage();
            $msg .= " | DSN=" . $dsn;
            die($msg);
        }
    }

    // Compat si ton code appelle Database::pdo()
    /**
     * Méthode : pdo()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public static function pdo(): PDO
    {
        return self::get();
    }
}
