<?php
namespace App\Modules\Reports\Models;

use App\Core\Database;

final class Report
{
    public static function createForBook(int $userId, int $bookId, string $reason, string $comment = ''): void
    {
        $pdo = Database::pdo();
        $sql = "INSERT IGNORE INTO reports (user_id, target_type, target_id, reason, comment, status, created_at)
                VALUES (?, 'book', ?, ?, ?, 'open', NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $bookId, $reason, $comment]);
    }

    public static function listForUser(int $userId, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        $pdo = Database::pdo();
        $sql = "SELECT r.*, b.title AS book_title, b.author AS book_author
                FROM reports r
                LEFT JOIN books b ON (r.target_type='book' AND b.id=r.target_id)
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countForUser(int $userId): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
