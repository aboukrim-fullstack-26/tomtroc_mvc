<?php
/**
 * app/Views/partials/pagination.php
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
 * Partial pagination (stable, non-AJAX)
 * Attend :
 * - $page (int)
 * - $totalPages (int)
 * Conserve les paramètres existants (q, author, status, etc.)
 */

$page = (int)($page ?? 1);
$totalPages = (int)($totalPages ?? 1);

$params = $_GET;
unset($params['page']);
$base = http_build_query($params);
$base = $base ? $base . '&' : '';
?>

<?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a class="pagination-link" href="?<?= $base ?>page=<?= $page - 1 ?>">← Précédent</a>
    <?php endif; ?>

    <span class="pagination-info">Page <?= $page ?> / <?= $totalPages ?></span>

    <?php if ($page < $totalPages): ?>
      <a class="pagination-link" href="?<?= $base ?>page=<?= $page + 1 ?>">Suivant →</a>
    <?php endif; ?>
  </div>
<?php endif; ?>
