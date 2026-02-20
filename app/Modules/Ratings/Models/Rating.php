<?php
namespace App\Modules\Ratings\Models;

use App\Core\Database;

/**
 * Rating (Model)
 * - Stocke une note 1..5 pour (user_id, book_id)
 * - Fournit stats pour l'affichage (moyenne, total, note utilisateur)
 *
 * IMPORTANT DB :
 * - Table `ratings` avec UNIQUE(user_id, book_id)
 */
final class Rating
{
    /** Enregistre (ou met à jour) une note */
    public static function rate(int $userId, int $bookId, int $rating): void
    {
        $pdo = Database::pdo();

        $sql = "INSERT INTO ratings (user_id, book_id, rating, created_at, updated_at)
                VALUES (?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), updated_at = NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $bookId, $rating]);
    }

    /** Moyenne des notes d'un livre */
    public static function avgForBook(int $bookId): float
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT AVG(rating) FROM ratings WHERE book_id = ?");
        $stmt->execute([$bookId]);
        $val = $stmt->fetchColumn();
        return $val !== null ? (float)$val : 0.0;
    }

    /** Nombre de notes d'un livre */
    public static function countForBook(int $bookId): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ratings WHERE book_id = ?");
        $stmt->execute([$bookId]);
        return (int)$stmt->fetchColumn();
    }

    /** Note d'un utilisateur sur un livre (ou null) */
    public static function userRating(int $userId, int $bookId): ?int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT rating FROM ratings WHERE user_id = ? AND book_id = ? LIMIT 1");
        $stmt->execute([$userId, $bookId]);
        $val = $stmt->fetchColumn();
        if ($val === false || $val === null) return null;
        return (int)$val;
    }

    /**
     * Stats pack pour la vue livre.
     * Compatibilité : renvoie à la fois (avg,count,user) ET (avg,cnt,user_rating)
     */
    public static function statsForBook(int $bookId, ?int $userId = null): array
    {
        $avg = self::avgForBook($bookId);
        $count = self::countForBook($bookId);
        $user = null;

        if ($userId !== null) {
            $user = self::userRating($userId, $bookId);
        }

        return [
            'avg' => $avg,
            'count' => $count,
            'cnt' => $count,              // alias attendu par show.php
            'user' => $user,
            'user_rating' => $user,       // alias éventuel
        ];
    }
}
