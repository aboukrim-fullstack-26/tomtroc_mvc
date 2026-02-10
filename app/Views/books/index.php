<?php
/**
 * app/Views/books/index.php
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
use App\Core\Helpers;

/**
 * Vue — /livres
 * Maquette: titre à gauche + champ recherche à droite.
 * Les filtres avancés (auteur + disponibilité) restent disponibles via <details>,
 * sans casser le rendu "pixel-perfect" de la V1.
 */
?>

<section class="container section books-page">

  <div class="books-header">
    <div class="books-title">
      <h1>Nos livres à l’échange</h1>
    </div>

    <!-- Recherche (maquette) -->
    <form class="books-search" method="get" action="<?= Helpers::url('/livres') ?>">
      <!-- Conserve les filtres si l'utilisateur a déjà filtré -->
      <?php if (!empty($author)): ?>
        <input type="hidden" name="author" value="<?= Helpers::e($author) ?>">
      <?php endif; ?>
      <?php if (!empty($status) && $status !== 'available'): ?>
        <input type="hidden" name="status" value="<?= Helpers::e($status) ?>">
      <?php endif; ?>

      <input
        type="text"
        name="q"
        placeholder="Rechercher un livre"
        value="<?= Helpers::e($q ?? '') ?>"
        aria-label="Rechercher un livre"
      >
    </form>
  </div>

  <!-- Filtres (non-AJAX) -->
  <details class="books-advanced" <?= (!empty($author) || (!empty($status) && $status !== 'available')) ? 'open' : '' ?>>
    <summary>Filtres</summary>

    <form class="books-filters" method="get" action="<?= Helpers::url('/livres') ?>">
      <input type="hidden" name="q" value="<?= Helpers::e($q ?? '') ?>">
      <select name="author">
        <option value="">Tous les auteurs</option>
        <?php foreach (($authors ?? []) as $a): ?>
          <option value="<?= Helpers::e($a) ?>" <?= ($author ?? '') === $a ? 'selected' : '' ?>>
            <?= Helpers::e($a) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="status">
        <option value="available" <?= ($status ?? 'available') === 'available' ? 'selected' : '' ?>>Disponibles</option>
        <option value="unavailable" <?= ($status ?? '') === 'unavailable' ? 'selected' : '' ?>>Indisponibles</option>
        <option value="all" <?= ($status ?? '') === 'all' ? 'selected' : '' ?>>Tous</option>
      </select>

      <div class="books-filters-actions">
        <button class="btn" type="submit">Filtrer</button>
        <a class="btn btn-primary" href="<?= Helpers::url('/livres') ?>">Afficher tous</a>
      </div>
    </form>
  </details>

  <?php if (!($hasResult ?? true)): ?>
    <div class="empty-state">
      <p>Aucun livre ne correspond à votre recherche.</p>
      <a class="btn btn-primary" href="<?= Helpers::url('/livres') ?>">Afficher tous</a>
    </div>
  <?php endif; ?>

  <div class="books-grid">
    <?php foreach (($books ?? []) as $b): ?>
      <?php
        $p = $b['photo_path'] ?? '';
        if ($p && str_starts_with($p, 'http')) $img = $p;
        elseif ($p) $img = BASE_URL . '/' . ltrim($p, '/');
        else $img = BASE_URL . '/assets/img/book-placeholder.jpg';

        $isUnavailable = isset($b['status']) && $b['status'] !== 'available';
      ?>
      <a class="book-card" href="<?= Helpers::url('/livre?id='.(int)$b['id']) ?>">
        <div class="book-card__img">
          <?php if ($isUnavailable): ?>
            <span class="book-badge book-badge--unavailable">non dispo.</span>
          <?php endif; ?>
          <img src="<?= Helpers::e($img) ?>" alt="<?= Helpers::e($b['title']) ?>">
        </div>

        <div class="book-card__body">
          <div class="book-card__title"><?= Helpers::e($b['title']) ?></div>
          <div class="book-card__author"><?= Helpers::e($b['author']) ?></div>
          <div class="book-card__owner">Vendu par : <?= Helpers::e($b['owner_pseudo'] ?? '') ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (($totalPages ?? 1) > 1): ?>
    <div class="pagination">
      <?php
        $params = $_GET;
        $current = (int)($page ?? 1);
        $tp = (int)$totalPages;
      ?>
      <?php if ($current > 1): ?>
        <?php $params['page'] = $current - 1; ?>
        <a class="pagination-link" href="?<?= http_build_query($params) ?>">← Précédent</a>
      <?php endif; ?>

      <span class="pagination-info">Page <?= $current ?> / <?= $tp ?></span>

      <?php if ($current < $tp): ?>
        <?php $params['page'] = $current + 1; ?>
        <a class="pagination-link" href="?<?= http_build_query($params) ?>">Suivant →</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</section>
