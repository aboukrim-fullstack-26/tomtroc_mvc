<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Pages statiques (Core)
 * - Politique de confidentialité
 * - Mentions légales
 *
 * Objectif : activer les liens du footer sans dépendre d'un module.
 */
final class PagesController extends Controller
{
    public function privacy(): void
    {
        $this->render('pages/privacy');
    }

    public function legal(): void
    {
        $this->render('pages/legal');
    }
}
