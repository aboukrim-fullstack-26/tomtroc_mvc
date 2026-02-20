<?php
/**
 * Layout Header - Modular Safe v5.2
 * @author @aboukrim
 */

use App\Core\Auth;
use App\Core\Helpers;
use App\Core\ModuleManager;

$modules = new ModuleManager();

$unread = 0;
if (Auth::check() && $modules->isEnabled('Message') && class_exists('\\App\\Models\\Message')) {
    $unread = \App\Models\Message::unreadCountForUser(Auth::id());
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TomTroc</title>
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
      <?php if ($modules->isEnabled('Book')): ?>
        <a href="<?= Helpers::url('/livres') ?>">Nos livres à l'échange</a>
      <?php endif; ?>
      <?php if ($modules->isEnabled('TopBooks')): ?>
        <a href="<?= Helpers::url('/top-livres') ?>">Top 5</a>
      <?php endif; ?>
    </nav>

    <span class="nav-divider" aria-hidden="true"></span>

    <nav class="menu right nav-right">

      <?php if ($modules->isEnabled('Message')): ?>
        <a class="nav-iconlink" href="<?= Helpers::url('/messagerie') ?>">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
          </svg>
          <span>Messagerie</span>
          <?php if ($unread > 0): ?>
            <span class="count-badge"><?= (int)$unread ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>

      <?php if (Auth::check() && $modules->isEnabled('Notifications')): ?>
        <?php
          $notifCount = 0;
          if (class_exists(\App\Modules\Notifications\Models\Notification::class)) {
            $notifCount = \App\Modules\Notifications\Models\Notification::unreadCountForUser(Auth::id());
          }
        ?>
        <a class="nav-iconlink" href="<?= Helpers::url('/notifications') ?>">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8a6 6 0 10-12 0c0 7-3 7-3 7h18s-3 0-3-7"/>
            <path d="M13.73 21a2 2 0 01-3.46 0"/>
          </svg>
          <span>Notifications</span>
          <?php if ($notifCount > 0): ?>
            <span class="count-badge"><?= (int)$notifCount ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>

      <?php if (Auth::check() && $modules->isEnabled('Exchange')): ?>
        <a href="<?= Helpers::url('/demandes') ?>">Échanges</a>
      <?php endif; ?>

      <?php if (Auth::check() && $modules->isEnabled('Favorites')): ?>
        <a class="nav-iconlink" href="<?= Helpers::url('/favoris') ?>">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 17.27l-5.18 3.05 1.64-5.81L3 9.24l6-.52L12 3l3 5.72 6 .52-4.46 5.27 1.64 5.81L12 17.27z"/>
          </svg>
          <span>Favoris</span>
        </a>
      <?php endif; ?>

      <?php if (Auth::check() && $modules->isEnabled('Reports')): ?>
        <a href="<?= Helpers::url('/mes-signalements') ?>">Mes signalements</a>
      <?php endif; ?>

      <?php if (Auth::check()): ?>
        <a class="nav-iconlink" href="<?= Helpers::url('/mon-compte') ?>">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21a8 8 0 0 0-16 0"/>
            <circle cx="12" cy="8" r="4"/>
          </svg>
          <span>Mon compte</span>
        </a>
        <?php if ($modules->isEnabled('Auth')): ?>
          <a href="<?= Helpers::url('/deconnexion') ?>">Déconnexion</a>
        <?php endif; ?>
      <?php else: ?>
        <?php if ($modules->isEnabled('Auth')): ?>
          <a href="<?= Helpers::url('/connexion') ?>">Connexion</a>
          <a href="<?= Helpers::url('/inscription') ?>">Inscription</a>
        <?php endif; ?>
      <?php endif; ?>

    </nav>
  </div>
</header>
<main class="site-main">