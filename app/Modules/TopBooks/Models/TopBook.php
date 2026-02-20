<?php
namespace App\Modules\TopBooks\Models;

use App\Core\Database;

/**
 * Calcule un Top N livres
 * Stratégie simple: moyenne des notes (ratings) + nombre de notes, fallback = livres récents.
 */
final class TopBook
{
    public static function top(int $limit = 5): array
    {
        $pdo = Database::pdo();

        // Si la table ratings existe, on fait un TOP par moyenne + volume
        try {
            $sql = "SELECT b.*, u.pseudo AS owner_pseudo,
                           AVG(r.rating) AS avg_rating,
                           COUNT(r.id) AS ratings_count
                    FROM books b
                    JOIN users u ON u.id = b.user_id
                    LEFT JOIN ratings r ON r.book_id = b.id
                    GROUP BY b.id
                    ORDER BY avg_rating DESC, ratings_count DESC, b.created_at DESC
                    LIMIT ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            // fallback: derniers livres
            $stmt = $pdo->prepare("SELECT b.*, u.pseudo AS owner_pseudo
                                   FROM books b
                                   JOIN users u ON u.id = b.user_id
                                   ORDER BY b.created_at DESC
                                   LIMIT ?");
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }
}
