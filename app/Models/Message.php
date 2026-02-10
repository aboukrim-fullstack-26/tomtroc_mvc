<?php
/**
 * app/Models/Message.php
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

/*
 * TomTroc — Model
 * - Encapsule l'accès à la base (PDO)
 * - Porte la logique métier (CRUD + requêtes)
 * - Utilisé par les contrôleurs
 */

namespace App\Models;

use App\Core\Database;

/**
 * Messagerie - gestion des messages + compteur non-lus (optionnel).
 *
 * ✅ Compatible avec la V1 : si la colonne `is_read` n'existe pas, le compteur reste à 0
 * et les méthodes "lu/non-lu" deviennent inertes (aucun crash).
 */
final class Message
{
    private static ?bool $hasReadColumn = null;

    /**
     * Méthode : supportsReadFlag()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    private static function supportsReadFlag(): bool
    {
        if (self::$hasReadColumn !== null) {
            return self::$hasReadColumn;
        }

        try {
            $pdo = Database::pdo();
            $stmt = $pdo->query("SHOW COLUMNS FROM messages LIKE 'is_read'");
            $row = $stmt->fetch();
            self::$hasReadColumn = (bool)$row;
        } catch (\Throwable $e) {
            self::$hasReadColumn = false;
        }

        return self::$hasReadColumn;
    }

    /**
     * Méthode : forConversation()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public static function forConversation(int $conversationId, int $page = 1, int $perPage = 10): array
    {
        // Pagination du fil (10 messages par page par défaut)
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);

        $pdo = Database::pdo();
        $offset = max(0, ($page - 1) * $perPage);

        $stmt = $pdo->prepare(
            "SELECT * FROM messages
             WHERE conversation_id = ?
             ORDER BY created_at ASC
             LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, $conversationId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Méthode : countForConversation()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public static function countForConversation(int $conversationId): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM messages WHERE conversation_id = ?");
        $stmt->execute([$conversationId]);
        $row = $stmt->fetch();
        return (int)($row['cnt'] ?? 0);
    }

/**
 * Méthode : create()
 * Rôle : logique du composant (Controller/Model/Core).
 * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
 */
public static function create(int $conversationId, int $senderId, string $body): void
    {
        $pdo = Database::pdo();

        if (self::supportsReadFlag()) {
            $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, body, is_read) VALUES (?, ?, ?, 0)");
            $stmt->execute([$conversationId, $senderId, $body]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, body) VALUES (?, ?, ?)");
            $stmt->execute([$conversationId, $senderId, $body]);
        }

        // Maj du "dernier message"
        $pdo->prepare("UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")
            ->execute([$conversationId]);
    }

    /**
     * Compteur de messages non lus pour un utilisateur.
     * - Retourne 0 si la colonne `is_read` n'existe pas (compat V1).
     */
    /*public static function unreadCountForUser(int $userId): int
    {
        if (!self::supportsReadFlag()) {
            return 0;
        }

        $pdo = Database::pdo();

        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS cnt
            FROM messages m
            JOIN conversations c ON c.id = m.conversation_id
            WHERE m.is_read = 0
              AND m.sender_id <> :uid
              AND (c.user_one_id = :uid OR c.user_two_id = :uid)
        ");
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();

        return (int)($row['cnt'] ?? 0);
    }*/
	
	public static function unreadCountForUser(int $userId): int
	{
		if (!self::supportsReadFlag()) {
			return 0;
		}

		$pdo = Database::pdo();

		$stmt = $pdo->prepare("
			SELECT COUNT(*) AS cnt
			FROM messages m
			JOIN conversations c ON c.id = m.conversation_id
			WHERE m.is_read = 0
			  AND m.sender_id <> ?
			  AND (c.user_one_id = ? OR c.user_two_id = ?)
		");

		// ✅ 3 placeholders => 3 paramètres
		$stmt->execute([$userId, $userId, $userId]);

		$row = $stmt->fetch();
		return (int)($row['cnt'] ?? 0);
	}


    /**
     * Marque comme lus les messages d'une conversation (ceux envoyés par l'autre).
     * - Ne fait rien si la colonne `is_read` n'existe pas (compat V1).
     */
    public static function markConversationRead(int $conversationId, int $userId): void
    {
        if (!self::supportsReadFlag()) {
            return;
        }

        $pdo = Database::pdo();

        // read_at est optionnel : on tente avec, sinon sans
        try {
            $pdo->prepare("
                UPDATE messages
                SET is_read = 1, read_at = CURRENT_TIMESTAMP
                WHERE conversation_id = ?
                  AND sender_id <> ?
                  AND is_read = 0
            ")->execute([$conversationId, $userId]);
        } catch (\Throwable $e) {
            $pdo->prepare("
                UPDATE messages
                SET is_read = 1
                WHERE conversation_id = ?
                  AND sender_id <> ?
                  AND is_read = 0
            ")->execute([$conversationId, $userId]);
        }
    }
}
