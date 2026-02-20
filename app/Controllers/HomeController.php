<?php

/*
 * TomTroc — HomeController
 * - Page d'accueil (GET /)
 * - Récupère les derniers livres + TopBooks si module activé
 * - Passe un token CSRF à la vue pour les CTA POST (ex: demande d'échange)
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\ModuleManager;
use App\Models\Book;
use App\Modules\Exchange\Models\ExchangeRequest;

final class HomeController extends Controller
{
    public function index(): void
    {
        $modules = new ModuleManager();

        // Derniers livres (si le module Books est actif)
        $latest = ($modules->isEnabled('Book') ? Book::latest(4) : []);

        // Top 5 (si le module TopBooks est actif)
        $topBooks = [];
        if ($modules->isEnabled('TopBooks') && class_exists(\App\Modules\TopBooks\Models\TopBook::class)) {
            $topBooks = \App\Modules\TopBooks\Models\TopBook::top(5);
        }

        // Ajout d'un indicateur métier "demande d'échange en attente" (pending)
        // pour le slider TopBooks :
        // - si le module Exchange est actif
        // - et si l'utilisateur est connecté
        // => on évite d'afficher le bouton "Demander un échange" si une demande pending existe déjà
        if ($modules->isEnabled('Exchange') && Auth::check() && !empty($topBooks)) {
            foreach ($topBooks as &$tb) {
                $tb['has_pending_exchange'] = ExchangeRequest::hasPendingForBook(
                    Auth::id(),
                    (int)($tb['id'] ?? 0)
                );
            }
            unset($tb);
        }

        $this->render('home/index', [
            'latest' => $latest,
            'topBooks' => $topBooks,
            'csrf' => Csrf::token(),
            'modules' => $modules,
        ]);
    }
}