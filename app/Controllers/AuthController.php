<?php
/**
 * app/Controllers/AuthController.php
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
 * TomTroc — Controller
 * - Reçoit la requête (GET/POST)
 * - Valide/autorise (Auth, CSRF)
 * - Appelle les modèles (DB + logique métier)
 * - Rend une vue (View::render) ou redirige après POST (PRG)
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Models\User;

final class AuthController extends Controller
{
    /**
     * Méthode : register()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function register(): void
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requirePostCsrf();

            $pseudo = trim((string)($_POST['pseudo'] ?? ''));
            $email  = trim((string)($_POST['email'] ?? ''));
            $pass   = (string)($_POST['password'] ?? '');

            if ($pseudo === '' || strlen($pseudo) < 3) $errors[] = "Le pseudo doit faire au moins 3 caractères.";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Adresse email invalide.";
            if (strlen($pass) < 8) $errors[] = "Le mot de passe doit faire au moins 8 caractères.";

            if (User::findByEmail($email)) $errors[] = "Un compte existe déjà avec cet email.";
            if (User::findByPseudo($pseudo)) $errors[] = "Ce pseudo est déjà pris.";

            if (!$errors) {
                $userId = User::create($pseudo, $email, $pass);
                Auth::login($userId);
                Helpers::redirect('/mon-compte');
            }
        }

        $this->render('auth/register', ['errors' => $errors, 'csrf' => Csrf::token()]);
    }

    /**
     * Méthode : login()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function login(): void
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requirePostCsrf();

            $email = trim((string)($_POST['email'] ?? ''));
            $pass  = (string)($_POST['password'] ?? '');

            $user = User::findByEmail($email);
            if (!$user || !password_verify($pass, $user['password_hash'])) {
                $errors[] = "Email ou mot de passe incorrect.";
            } else {
                Auth::login((int)$user['id']);
                Helpers::redirect('/mon-compte');
            }
        }

        $this->render('auth/login', ['errors' => $errors, 'csrf' => Csrf::token()]);
    }

    /**
     * Méthode : logout()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function logout(): void
    {
        Auth::logout();
        Helpers::redirect('/');
    }
}
