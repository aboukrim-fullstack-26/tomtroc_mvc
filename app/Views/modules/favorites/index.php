<?php
use App\Core\Helpers;
?>
<section class="container section">
  <h1 class="page-title">Mes favoris</h1>

  <?php if (empty($favorites)): ?>
    <p class="muted">Aucun favori pour le moment.</p>
    <p><a class="btn" href="<?= Helpers::url('/livres') ?>">Voir les livres</a></p>
  <?php else: ?>
    <div class="favorites-grid">
      <?php foreach ($favorites as $fav): ?>
        <article class="favorite-card">
          <div class="favorite-top">
            <div class="favorite-meta">
              <h3><?= Helpers::e($fav['title']) ?></h3>
              <div class="muted"><?= Helpers::e($fav['author']) ?></div>
              <div class="muted">Ajouté le <?= Helpers::e(date('d/m/Y', strtotime((string)$fav['created_at']))) ?></div>
            </div>
          </div>

          <div class="favorite-actions">
            <a class="btn" href="<?= Helpers::url('/livre?id=' . (int)$fav['book_id']) ?>">Voir</a>

            <form method="post" action="<?= Helpers::url('/favori/supprimer') ?>" class="inline">
              <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
              <input type="hidden" name="book_id" value="<?= (int)$fav['book_id'] ?>">
              <input type="hidden" name="back" value="<?= Helpers::e($_SERVER['REQUEST_URI'] ?? '/favoris') ?>">
              <button class="btn btn-outline" type="submit">Retirer</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
