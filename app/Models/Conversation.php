<?php

/*
 * TomTroc — Model
 * - Encapsule l'accès à la base (PDO)
 * - Porte la logique métier (CRUD + requêtes)
 * - Utilisé par les contrôleurs
 */

namespace App\Models;

use App\Core\Database;

final class Conversation
{
    /**
     * Liste des conversations d'un utilisateur + infos maquette :
     * - peer_user_id, peer_pseudo, peer_avatar
     * - last_at, last_body
     */
    public static function forUser(int $userId): array
    {
        $pdo = Database::pdo();

        $sql = "
            SELECT
                c.id,
                c.user_one_id,
                c.user_two_id,
                c.updated_at,
                c.created_at,
                CASE WHEN c.user_one_id = ? THEN c.user_two_id ELSE c.user_one_id END AS peer_user_id,
                u.pseudo AS peer_pseudo,
                u.avatar_path AS peer_avatar,
                lm.created_at AS last_at,
                lm.body AS last_body
            FROM conversations c
            JOIN users u
              ON u.id = CASE WHEN c.user_one_id = ? THEN c.user_two_id ELSE c.user_one_id END
            LEFT JOIN messages lm
              ON lm.id = (
                  SELECT m2.id
                  FROM messages m2
                  WHERE m2.conversation_id = c.id
                  ORDER BY m2.created_at DESC
                  LIMIT 1
              )
            WHERE c.user_one_id = ? OR c.user_two_id = ?
            ORDER BY COALESCE(lm.created_at, c.updated_at, c.created_at) DESC
        ";

        $stmt = $pdo->prepare($sql);
        // ⚠️ 4 placeholders => 4 valeurs
        $stmt->execute([$userId, $userId, $userId, $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une conversation si l'utilisateur y participe
     * + peer_user_id pour charger le profil en face
     */
    public static function findForUser(int $conversationId, int $userId): ?array
    {
        $pdo = Database::pdo();

        $sql = "
            SELECT
                c.*,
                CASE WHEN c.user_one_id = ? THEN c.user_two_id ELSE c.user_one_id END AS peer_user_id
            FROM conversations c
            WHERE c.id = ?
              AND (c.user_one_id = ? OR c.user_two_id = ?)
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        // 4 placeholders
        $stmt->execute([$userId, $conversationId, $userId, $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Récupère ou crée une conversation entre 2 utilisateurs
     */
    public static function getOrCreate(int $a, int $b): int
    {
        $pdo = Database::pdo();

        // Cherche dans les deux sens
        $stmt = $pdo->prepare("
            SELECT id FROM conversations
            WHERE (user_one_id = ? AND user_two_id = ?)
               OR (user_one_id = ? AND user_two_id = ?)
            LIMIT 1
        ");
        $stmt->execute([$a, $b, $b, $a]);

        if ($row = $stmt->fetch()) {
            return (int)$row['id'];
        }

        // Crée
        $stmt = $pdo->prepare("INSERT INTO conversations (user_one_id, user_two_id) VALUES (?, ?)");
        $stmt->execute([$a, $b]);

        return (int)$pdo->lastInsertId();
    }
}
