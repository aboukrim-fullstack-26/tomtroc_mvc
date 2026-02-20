<?php
namespace App\Modules\Exchange\Models;

use App\Core\Database;

final class ExchangeRequest
{
    public static function createFromBook(int $requesterId, int $bookId, string $message = ''): void
    {
        $pdo = Database::pdo();

        $st = $pdo->prepare("SELECT id, user_id, title FROM books WHERE id = ? LIMIT 1");
        $st->execute([$bookId]);
        $book = $st->fetch();
        if (!$book) return;

        $ownerId = (int)$book['user_id'];
        if ($ownerId === $requesterId) return;

        // Empêche d'envoyer plusieurs demandes "pending" pour le même livre
        $check = $pdo->prepare("SELECT id FROM exchange_requests WHERE book_id = ? AND requester_id = ? AND status = 'pending' LIMIT 1");
        $check->execute([$bookId, $requesterId]);
        if ($check->fetch()) return;


        $sql = "INSERT INTO exchange_requests (book_id, requester_id, owner_id, message, status, created_at)
                VALUES (?, ?, ?, ?, 'pending', NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$bookId, $requesterId, $ownerId, $message]);

        // ✅ Notification (module Notifications) : message = titre du livre, lien = détail du livre
        $notifClass = '\\App\\Modules\\Notifications\\Models\\Notification';
        if (class_exists($notifClass)) {
            $bookTitle = trim((string)($book['title'] ?? ''));
            $notifClass::create(
                $ownerId,
                'exchange',
                $bookTitle !== '' ? $bookTitle : 'Nouvelle demande d\'échange',
                '/livre?id=' . (int)$bookId
            );
        }
    }

    
    /**
     * Retourne une demande "pending" déjà envoyée par $userId pour $bookId, ou null.
     * Utilisé sur la page détail livre pour remplacer le bouton par un message.
     */
    public static function pendingForRequesterAndBook(int $userId, int $bookId): ?array
    {
        $pdo = Database::pdo();
        $st = $pdo->prepare("SELECT * FROM exchange_requests WHERE book_id = ? AND requester_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
        $st->execute([$bookId, $userId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /**
     * Historique des demandes pour un livre (visible par owner ou requester).
     * Affiche envoyées / reçues + statuts.
     */
    public static function historyForBook(int $bookId, int $userId): array
    {
        $pdo = Database::pdo();
        $st = $pdo->prepare("
            SELECT er.*, u.pseudo AS requester_pseudo
            FROM exchange_requests er
            JOIN users u ON u.id = er.requester_id
            WHERE er.book_id = ?
              AND (er.owner_id = ? OR er.requester_id = ?)
            ORDER BY er.created_at DESC
        ");
        $st->execute([$bookId, $userId, $userId]);
        return $st->fetchAll();
    }

    /**
     * Listing paginé de TOUTES les demandes d'un utilisateur (envoyées + reçues).
     * Filtre possible via $filters :
     * - box: all|sent|received
     * - status: pending|accepted|rejected
     * - q: recherche (titre/auteur/pseudo)
     */
    public static function paginateAllForUser(int $userId, int $page, int $perPage, array $filters): array
	{
		$pdo = Database::pdo();

		$page = max(1, (int)$page);
		$perPage = max(1, (int)$perPage);
		$offset = ($page - 1) * $perPage;

		// sécurisation
		$limitSql = (int)$perPage;
		$offsetSql = (int)$offset;
		$userIdSql = (int)$userId;

		// tri autorisé uniquement sur colonnes connues
		$sortMap = [
			'date' => 'er.created_at',
			'status' => 'er.status',
			'book' => 'b.title',
			'requester' => 'u.pseudo',
			'box' => "(er.requester_id = $userIdSql)"
		];

		$sort = $sortMap[$filters['sort'] ?? 'date'] ?? 'er.created_at';
		$dir = (($filters['dir'] ?? 'desc') === 'asc') ? 'ASC' : 'DESC';

		// IMPORTANT : on utilise uid_owner et uid_requester séparés
		$where = [
			"(er.owner_id = :uid_owner OR er.requester_id = :uid_requester)"
		];

		$params = [
			':uid_owner' => $userId,
			':uid_requester' => $userId
		];

		if (!empty($filters['status']) &&
			in_array($filters['status'], ['pending','accepted','rejected'], true))
		{
			$where[] = "er.status = :status";
			$params[':status'] = $filters['status'];
		}

		if (!empty($filters['q']))
		{
			$where[] = "(b.title LIKE :q OR b.author LIKE :q OR u.pseudo LIKE :q)";
			$params[':q'] = '%' . $filters['q'] . '%';
		}

		$sql = "
			SELECT
				er.*,
				b.title AS book_title,
				b.author AS book_author,
				u.pseudo AS requester_pseudo,
				CASE
					WHEN er.requester_id = $userIdSql THEN 'sent'
					ELSE 'received'
				END AS box
			FROM exchange_requests er
			JOIN books b ON b.id = er.book_id
			JOIN users u ON u.id = er.requester_id
			WHERE " . implode(' AND ', $where) . "
			ORDER BY $sort $dir
			LIMIT $limitSql OFFSET $offsetSql
		";

		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);

		return $stmt->fetchAll();
	}

    public static function countAllForUser(int $userId, array $filters): int
	{
		$pdo = Database::pdo();

		$where = [
			"(er.owner_id = :uid_owner OR er.requester_id = :uid_requester)"
		];

		$params = [
			':uid_owner' => $userId,
			':uid_requester' => $userId
		];

		if (!empty($filters['status']) &&
			in_array($filters['status'], ['pending','accepted','rejected'], true))
		{
			$where[] = "er.status = :status";
			$params[':status'] = $filters['status'];
		}

		if (!empty($filters['q']))
		{
			$where[] = "(b.title LIKE :q OR b.author LIKE :q OR u.pseudo LIKE :q)";
			$params[':q'] = '%' . $filters['q'] . '%';
		}

		$sql = "
			SELECT COUNT(*)
			FROM exchange_requests er
			JOIN books b ON b.id = er.book_id
			JOIN users u ON u.id = er.requester_id
			WHERE " . implode(' AND ', $where);

		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);

		return (int)$stmt->fetchColumn();
	}


    public static function updateStatus(int $id, int $ownerId, string $status): void
    {
        if (!in_array($status, ['accepted','rejected','pending'], true)) return;

        $pdo = Database::pdo();
        $stmt = $pdo->prepare("UPDATE exchange_requests
                               SET status = ?, updated_at = NOW()
                               WHERE id = ? AND owner_id = ?");
        $stmt->execute([$status, $id, $ownerId]);
    }

    public static function forOwner(int $ownerId, int $page, int $perPage, array $filters): array
    {
        $offset = ($page - 1) * $perPage;
        $pdo = Database::pdo();

        $sortMap = [
            'date' => 'er.created_at',
            'status' => 'er.status',
            'book' => 'b.title',
            'requester' => 'u.pseudo',
        ];
        $sort = $sortMap[$filters['sort'] ?? 'date'] ?? 'er.created_at';
        $dir  = ($filters['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

        $where = ["er.owner_id = :owner"];
        $params = ['owner' => $ownerId];

        if (!empty($filters['status']) && in_array($filters['status'], ['pending','accepted','rejected'], true)) {
            $where[] = "er.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $where[] = "(b.title LIKE :q OR u.pseudo LIKE :q)";
            $params['q'] = '%' . $filters['q'] . '%';
        }

        $sql = "SELECT er.*,
                       b.title AS book_title,
                       b.author AS book_author,
                       u.pseudo AS requester_pseudo
                FROM exchange_requests er
                JOIN books b ON b.id = er.book_id
                JOIN users u ON u.id = er.requester_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY $sort $dir
                LIMIT :limit OFFSET :offset";

        $st = $pdo->prepare($sql);
        foreach ($params as $k => $v) $st->bindValue(':' . $k, $v);
        $st->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public static function countForOwner(int $ownerId, array $filters): int
    {
        $pdo = Database::pdo();

        if (!empty($filters['q'])) {
            $sql = "SELECT COUNT(*)
                    FROM exchange_requests er
                    JOIN books b ON b.id = er.book_id
                    JOIN users u ON u.id = er.requester_id
                    WHERE er.owner_id = :owner AND (b.title LIKE :q OR u.pseudo LIKE :q)";
            $st = $pdo->prepare($sql);
            $st->execute(['owner' => $ownerId, 'q' => '%' . $filters['q'] . '%']);
            return (int)$st->fetchColumn();
        }

        $where = ["owner_id = :owner"];
        $params = ['owner' => $ownerId];

        if (!empty($filters['status']) && in_array($filters['status'], ['pending','accepted','rejected'], true)) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        $sql = "SELECT COUNT(*) FROM exchange_requests WHERE " . implode(' AND ', $where);
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return (int)$st->fetchColumn();
    }
	
	public static function hasPendingForBook(int $userId, int $bookId): bool
	{
		$pdo = Database::pdo();

		$stmt = $pdo->prepare("
			SELECT id
			FROM exchange_requests
			WHERE requester_id = ?
			AND book_id = ?
			AND status = 'pending'
			LIMIT 1
		");

		$stmt->execute([$userId, $bookId]);

		return (bool)$stmt->fetch();
	}
}
