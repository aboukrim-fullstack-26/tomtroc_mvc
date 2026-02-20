<?php
/**
 * Model TopBooks (Top 5)
 * Auteur : @aboukrim
 */

namespace App\Modules\TopBooks\Models;

use App\Core\Database;

final class TopBooks
{
    /**
     * @return array<int, array<string,mixed>>
     */
    public static function top5(): array
    {
        $pdo = Database::pdo();

        // Si la table favorites existe, top = les plus favoris
        $hasFavorites = false;
        try {
            $pdo->query('SELECT 1 FROM favorites LIMIT 1');
            $hasFavorites = true;
        } catch (\Throwable $e) {
            $hasFavorites = false;
        }

        if ($hasFavorites) {
            $sql = "
              SELECT b.*, u.pseudo AS owner_pseudo, COUNT(f.id) AS fav_count
              FROM books b
              JOIN users u ON u.id = b.user_id
              LEFT JOIN favorites f ON f.book_id = b.id
              WHERE b.status = 'available'
              GROUP BY b.id
              ORDER BY fav_count DESC, b.created_at DESC
              LIMIT 5
            ";
            return $pdo->query($sql)->fetchAll();
        }

        $sql = "
          SELECT b.*, u.pseudo AS owner_pseudo
          FROM books b
          JOIN users u ON u.id = b.user_id
          WHERE b.status = 'available'
          ORDER BY b.created_at DESC
          LIMIT 5
        ";
        return $pdo->query($sql)->fetchAll();
    }
}
