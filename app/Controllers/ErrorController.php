<?php
/**
 * app/Controllers/ErrorController.php
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

use App\Core\Controller;

final class ErrorController extends Controller
{
    /**
     * Méthode : notFound()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function notFound(): void
    {
        $this->render('home/404');
    }
}
