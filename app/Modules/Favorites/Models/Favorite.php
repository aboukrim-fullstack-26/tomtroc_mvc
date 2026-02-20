<?php
namespace App\Modules\Favorites\Models;

use App\Core\Database;
//use App\Core\Auth;

/**
 * Favorite (Model)
 * - CRUD DB pour favoris
 */
final class Favorite
{
    public static function add(int $userId, int $bookId): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("INSERT IGNORE INTO favorites (user_id, book_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $bookId]);
		
		// ✅ Notification (si module Notifications actif) : titre = titre du livre, lien = détail
		/*$notifClass = '\\App\\Modules\\Notifications\\Models\\Notification';
		if (class_exists($notifClass)) {
			try {
				$pdo = Database::pdo();
				$st = $pdo->prepare("SELECT title FROM books WHERE id = ? LIMIT 1");
				$st->execute([$bookId]);
				$title = trim((string)$st->fetchColumn());
				$notifClass::create(Auth::id(), 'favorite', $title !== '' ? $title : 'Ajouté aux favoris', '/livre?id=' . $bookId);
			} catch (\PDOException $e) {
				// ignore
			}
		}*/
		
    }

    public static function remove(int $userId, int $bookId): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$userId, $bookId]);
    }

    public static function exists(int $userId, int $bookId): bool
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND book_id = ? LIMIT 1");
        $stmt->execute([$userId, $bookId]);
        return (bool)$stmt->fetchColumn();
    }

    public static function listForUser(int $userId): array
    {
        $pdo = Database::pdo();
        $sql = "SELECT f.created_at, f.book_id,
                       b.title, b.author, b.photo_path, b.status
                FROM favorites f
                JOIN books b ON b.id = f.book_id
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
