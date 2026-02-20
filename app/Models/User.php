<?php

/*
 * TomTroc — Model
 * - Encapsule l'accès à la base (PDO)
 * - Porte la logique métier (CRUD + requêtes)
 * - Utilisé par les contrôleurs
 */

namespace App\Models;

use App\Core\Database;

final class User
{
    public static function findById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT id, pseudo, email, avatar_path, created_at, password_hash FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    public static function findByPseudo(string $pseudo): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE pseudo = ?');
        $stmt->execute([$pseudo]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    public static function create(string $pseudo, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = Database::pdo()->prepare('INSERT INTO users (pseudo, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$pseudo, $email, $hash]);
        return (int)Database::pdo()->lastInsertId();
    }

    public static function update(int $id, string $pseudo, string $email, ?string $password, ?array $avatarFile, ?string $avatarKeep): void
    {
        $pdo = Database::pdo();

        $avatarPath = $avatarKeep;
        if ($avatarFile && ($avatarFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $avatarPath = Uploads::storeImage($avatarFile, 'avatars');
        }

        if ($password !== null && $password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET pseudo = ?, email = ?, password_hash = ?, avatar_path = ? WHERE id = ?');
            $stmt->execute([$pseudo, $email, $hash, $avatarPath, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET pseudo = ?, email = ?, avatar_path = ? WHERE id = ?');
            $stmt->execute([$pseudo, $email, $avatarPath, $id]);
        }
    }
}
