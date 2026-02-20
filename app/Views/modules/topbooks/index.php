<?php
use App\Core\Helpers;
?>

<section class="container section topbooks-page">
  <div class="section-head">
    <h1 class="section-title">Top 5 des livres</h1>
    <a class="btn outline" href="<?= Helpers::url('/livres') ?>">Retour aux livres</a>
  </div>

  <p class="muted">Classement basé sur les notes ⭐ (moyenne + nombre de votes) si le module Ratings est actif, sinon sur les livres récents.</p>

  <?php if (empty($books)): ?>
    <p>Aucun livre à afficher.</p>
  <?php else: ?>
    <div class="books-grid">
      <?php foreach ($books as $b): ?>
        <?php
          $photo = $b['photo_path'] ?? '';
          if ($photo && str_starts_with($photo, 'http')) $src = $photo;
          elseif ($photo) $src = BASE_URL . '/' . $photo;
          else $src = BASE_URL . '/assets/img/book-placeholder.jpg';
        ?>
        <article class="book-card">
          <a class="book-card__link" href="<?= Helpers::url('/livre?id=' . (int)$b['id']) ?>">
            <img class="book-card__img" src="<?= htmlspecialchars($src) ?>" alt="<?= Helpers::e($b['title']) ?>">
            <div class="book-card__body">
              <h3 class="book-card__title"><?= Helpers::e($b['title']) ?></h3>
              <div class="muted"><?= Helpers::e($b['author'] ?? '') ?></div>
              <?php if (!empty($b['avg_rating'])): ?>
                <div class="small muted">⭐ <?= number_format((float)$b['avg_rating'], 1) ?> (<?= (int)($b['ratings_count'] ?? 0) ?>)</div>
              <?php endif; ?>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
