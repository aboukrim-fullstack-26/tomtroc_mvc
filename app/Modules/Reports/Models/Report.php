<?php
namespace App\Modules\Reports\Models;

use App\Core\Database;

/**
 * Module Reports — Model
 * - Signalements utilisateur (livre / message)
 * - CRUD minimal + listing paginé/filtré pour "Mes signalements"
 *
 * Notes techniques :
 * - Tri whitelisté (évite injection via ORDER BY)
 * - LIMIT/OFFSET injectés en dur après cast int (évite HY093 + compat)
 * - Recherche sur reason/comment + métadonnées de livre si target_type=book
 */
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

    /**
     * Listing paginé + filtres (Mes signalements)
     *
     * $filters:
     * - q: recherche (reason/comment + title/author si book)
     * - status: open|reviewed|closed|'' (tous)
     * - type: book|message|'' (tous)
     * - reason: clé motif (spam/fake/...)|'' (tous)
     * - sort: date|status|type|reason
     * - dir: asc|desc
     */
    public static function paginateForUser(int $userId, int $page, int $perPage, array $filters): array
    {
        $pdo = Database::pdo();

        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = (int)(($page - 1) * $perPage);

        $limitSql  = (int)$perPage;
        $offsetSql = (int)$offset;

        $sortMap = [
            'date'   => 'r.created_at',
            'status' => 'r.status',
            'type'   => 'r.target_type',
            'reason' => 'r.reason',
        ];
        $sort = $sortMap[$filters['sort'] ?? 'date'] ?? 'r.created_at';
        $dir  = (($filters['dir'] ?? 'desc') === 'asc') ? 'ASC' : 'DESC';

        $where = ["r.user_id = :uid"];
        $params = [':uid' => $userId];

        if (!empty($filters['status']) && in_array($filters['status'], ['open','reviewed','closed'], true)) {
            $where[] = "r.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['type']) && in_array($filters['type'], ['book','message'], true)) {
            $where[] = "r.target_type = :type";
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['reason'])) {
            // reason est un VARCHAR(50) => on peut filtrer strict
            $where[] = "r.reason = :reason";
            $params[':reason'] = $filters['reason'];
        }

        if (!empty($filters['q'])) {
            $where[] = "(r.reason LIKE :q1 OR r.comment LIKE :q2 OR b.title LIKE :q3 OR b.author LIKE :q4)";
            $q = '%' . $filters['q'] . '%';
            $params[':q1'] = $q;
            $params[':q2'] = $q;
            $params[':q3'] = $q;
            $params[':q4'] = $q;
        }

        $sql = "SELECT r.*, b.title AS book_title, b.author AS book_author
                FROM reports r
                LEFT JOIN books b ON (r.target_type='book' AND b.id=r.target_id)
                WHERE " . implode(' AND ', $where) . "
                ORDER BY $sort $dir
                LIMIT $limitSql OFFSET $offsetSql";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countForUserFiltered(int $userId, array $filters): int
    {
        $pdo = Database::pdo();

        $where = ["r.user_id = :uid"];
        $params = [':uid' => $userId];

        if (!empty($filters['status']) && in_array($filters['status'], ['open','reviewed','closed'], true)) {
            $where[] = "r.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['type']) && in_array($filters['type'], ['book','message'], true)) {
            $where[] = "r.target_type = :type";
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['reason'])) {
            $where[] = "r.reason = :reason";
            $params[':reason'] = $filters['reason'];
        }

        if (!empty($filters['q'])) {
            $where[] = "(r.reason LIKE :q1 OR r.comment LIKE :q2 OR b.title LIKE :q3 OR b.author LIKE :q4)";
            $q = '%' . $filters['q'] . '%';
            $params[':q1'] = $q;
            $params[':q2'] = $q;
            $params[':q3'] = $q;
            $params[':q4'] = $q;
        }

        $sql = "SELECT COUNT(*)
                FROM reports r
                LEFT JOIN books b ON (r.target_type='book' AND b.id=r.target_id)
                WHERE " . implode(' AND ', $where);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    // --- Compat: anciennes méthodes (utilisées dans certaines versions) ---
    public static function listForUser(int $userId, int $page, int $perPage): array
    {
        return self::paginateForUser($userId, $page, $perPage, []);
    }

    public static function countForUser(int $userId): int
    {
        return self::countForUserFiltered($userId, []);
    }
}
