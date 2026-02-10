<?php
/**
 * app/Views/books/show.php
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

$photo = $book['photo_path'] ?? null;

if ($photo && str_starts_with($photo, 'http')) {
  $src = $photo; // URL externe
} elseif ($photo) {
  // fichier local (photo_path = "uploads/xxx.jpg" ou "assets/...")
  $src = BASE_URL . '/' . ltrim($photo, '/');
} else {
  $src = BASE_URL . '/assets/img/book-placeholder.jpg';
}
?>

<!-- Breadcrumb (maquette) -->
<div class="container book-breadcrumb">
  <a href="<?= Helpers::url('/livres') ?>">Nos livres</a>
  <span class="sep">&gt;</span>
  <span><?= Helpers::e($book['title']) ?></span>
</div>

<section class="book-hero book-single">
  <!-- Colonne gauche : image -->
  <div class="book-cover">
    <img class="book-cover__img"
         src="<?= htmlspecialchars($src) ?>"
         alt="<?= htmlspecialchars($book['title']) ?>">
  </div>

  <!-- Colonne droite : contenu -->
  <div class="book-detail">
    <div class="book-detail__inner">
      <h1><?= Helpers::e($book['title']) ?></h1>
      <div class="muted">par <?= Helpers::e($book['author']) ?></div>

      <div class="separator"></div>

      <h3 class="tiny-title">DESCRIPTION</h3>
      <p class="book-desc">
        <?= nl2br(Helpers::e($book['description'] ?? '')) ?>
      </p>

      <h3 class="tiny-title">PROPRIÉTAIRE</h3>
      <div class="owner owner--small">
        <img class="avatar avatar--small"
             src="<?= Helpers::url(($owner['avatar_path'] ?? '') ?: '/assets/img/avatar-placeholder.jpg') ?>"
             alt="">
        <a href="<?= Helpers::url('/profil?id=' . (int)$owner['id']) ?>">
          <?= Helpers::e($owner['pseudo']) ?>
        </a>
      </div>

      <?php if (Auth::check() && (int)$owner['id'] !== Auth::id()): ?>
        <form method="post" action="<?= Helpers::url('/message/nouveau') ?>" class="message-cta">
          <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
          <input type="hidden" name="to_user_id" value="<?= (int)$owner['id'] ?>">
          <input type="hidden" name="body"
                 value="Bonjour, je suis intéressé(e) par votre livre &quot;<?= Helpers::e($book['title']) ?>&quot;.">
          <button class="btn full" type="submit">Envoyer un message</button>
        </form>

      <?php elseif (!Auth::check()): ?>
        <p class="center">
          <a class="btn" href="<?= Helpers::url('/connexion') ?>">Connectez-vous pour envoyer un message</a>
        </p>
      <?php endif; ?>
    </div>
  </div>
</section>
