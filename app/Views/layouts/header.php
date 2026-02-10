<?php
/**
 * app/Views/layouts/header.php
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
use App\Core\Auth;
use App\Core\Helpers;
use App\Models\Message;

$unread = 0;
if (Auth::check()) {
  $unread = Message::unreadCountForUser(Auth::id());
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TomTroc</title>
  <!--link rel="stylesheet" href="<?= Helpers::url('/assets/css/style.css') ?>"-->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a class="brand" href="<?= Helpers::url('/') ?>">
      <span class="logo">Tt</span>
      <span class="brand-name">Tom Troc</span>
    </a>

    <nav class="menu nav-mid">
      <a href="<?= Helpers::url('/') ?>">Accueil</a>
      <a href="<?= Helpers::url('/livres') ?>">Nos livres à l'échange</a>
    </nav>

    <span class="nav-divider" aria-hidden="true"></span>

    <nav class="menu right nav-right">
      <a class="nav-iconlink" href="<?= Helpers::url('/messagerie') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
        </svg>
        <span>Messagerie</span>
        <?php if ($unread > 0): ?>
        <span class="count-badge"><?= (int)$unread ?></span>
        <?php endif; ?>
      </a>
      <?php if (Auth::check()): ?>
        <a class="nav-iconlink" href="<?= Helpers::url('/mon-compte') ?>">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 21a8 8 0 0 0-16 0"/>
            <circle cx="12" cy="8" r="4"/>
          </svg>
          <span>Mon compte</span>
        </a>
        <a href="<?= Helpers::url('/deconnexion') ?>">Déconnexion</a>
      <?php else: ?>
        <a href="<?= Helpers::url('/connexion') ?>">Connexion</a>
      <?php endif; ?>
    </nav>

    <!-- Menu mobile -->
    <div class="burger">
      <input id="nav-toggle" type="checkbox" aria-hidden="true">
      <label class="burger-btn" for="nav-toggle" aria-label="Ouvrir le menu"><span></span></label>
      <div class="mobile-drawer">
        <div class="container">
          <nav class="mobile-links">
            <a href="<?= Helpers::url('/') ?>">Accueil</a>
            <a href="<?= Helpers::url('/livres') ?>">Nos livres à l'échange</a>
            <a href="<?= Helpers::url('/messagerie') ?>">Messagerie</a>
            <?php if (Auth::check()): ?>
              <a href="<?= Helpers::url('/mon-compte') ?>">Mon compte</a>
              <a href="<?= Helpers::url('/deconnexion') ?>">Déconnexion</a>
            <?php else: ?>
              <a href="<?= Helpers::url('/connexion') ?>">Connexion</a>
              <a href="<?= Helpers::url('/inscription') ?>">Inscription</a>
            <?php endif; ?>
          </nav>
        </div>
      </div>
    </div>
  </div>
</header>
<main class="site-main">
