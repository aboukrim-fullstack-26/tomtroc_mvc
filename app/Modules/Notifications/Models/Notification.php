<?php
namespace App\Modules\Notifications\Models;

use App\Core\Database;

/**
 * Notification (Model)
 *
 * Schéma table `notifications` (DB tomtroc) :
 * - id (PK)
 * - user_id (int)
 * - type (varchar)
 * - message (varchar)
 * - link (varchar, nullable)
 * - is_read (tinyint)
 * - created_at (datetime)
 * - read_at (datetime, nullable)
 *
 * NOTE : ce projet n'utilise PAS de colonnes `title` / `body`.
 * Le champ "message" sert de titre/texte court, et "link" permet de pointer
 * vers une page du site (ex: /livre?id=12, /messagerie).
 */
final class Notification
{
	/**
	 * Crée une notification (fail-safe : ne bloque jamais le parcours utilisateur).
	 */
	public static function create(int $userId, string $type, string $message, ?string $link = null): void
	{
		$pdo = Database::pdo();
		try {
			$st = $pdo->prepare(
				"INSERT INTO notifications (user_id, type, message, link, is_read, created_at)
				 VALUES (?, ?, ?, ?, 0, NOW())"
			);
			$st->execute([$userId, $type, $message, $link]);
		} catch (\PDOException $e) {
			// ignore
		}
	}

	public static function unreadCountForUser(int $userId): int
	{
		$pdo = Database::pdo();
		$st = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
		$st->execute([$userId]);
		return (int)$st->fetchColumn();
	}

	public static function markAllRead(int $userId): void
	{
		$pdo = Database::pdo();
		$st = $pdo->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0");
		$st->execute([$userId]);
	}

	public static function paginateForUser(int $userId, int $page, int $perPage, array $filters): array
	{
		$page = max(1, (int)$page);
		$perPage = max(1, (int)$perPage);
		$offset = (int)(($page - 1) * $perPage);
		$pdo = Database::pdo();

		$sortMap = [
			'date'  => 'created_at',
			'type'  => 'type',
			// compat UI : "Titre" trie sur la colonne `message`
			'title' => 'message',
			'read'  => 'is_read',
		];
		$sort = $sortMap[$filters['sort'] ?? 'date'] ?? 'created_at';
		$dir  = ($filters['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

		$where = ["user_id = :uid"];
		$params = ['uid' => $userId];

		if (($filters['read'] ?? '') !== '' && in_array((string)$filters['read'], ['0', '1'], true)) {
			$where[] = "is_read = :read";
			$params['read'] = (int)$filters['read'];
		}

		if (!empty($filters['q'])) {
			$where[] = "(message LIKE :q OR type LIKE :q)";
			$params['q'] = '%' . $filters['q'] . '%';
		}

		$sql = "SELECT * FROM notifications
				WHERE " . implode(' AND ', $where) . "
				ORDER BY $sort $dir
				LIMIT :limit OFFSET :offset";

		$st = $pdo->prepare($sql);
		foreach ($params as $k => $v) {
			$st->bindValue(':' . $k, $v);
		}
		$st->bindValue(':limit', $perPage, \PDO::PARAM_INT);
		$st->bindValue(':offset', $offset, \PDO::PARAM_INT);
		$st->execute();
		return $st->fetchAll();
	}

	public static function countForUser(int $userId, array $filters): int
	{
		$pdo = Database::pdo();
		$where = ["user_id = :uid"];
		$params = ['uid' => $userId];

		if (($filters['read'] ?? '') !== '' && in_array((string)$filters['read'], ['0', '1'], true)) {
			$where[] = "is_read = :read";
			$params['read'] = (int)$filters['read'];
		}

		if (!empty($filters['q'])) {
			$where[] = "(message LIKE :q OR type LIKE :q)";
			$params['q'] = '%' . $filters['q'] . '%';
		}

		$sql = "SELECT COUNT(*) FROM notifications WHERE " . implode(' AND ', $where);
		$st = $pdo->prepare($sql);
		$st->execute($params);
		return (int)$st->fetchColumn();
	}
}
