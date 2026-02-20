<?php
use App\Core\Helpers;

$read = $filters['read'] ?? '';
$q = $filters['q'] ?? '';
$sort = $filters['sort'] ?? 'date';
$dir = $filters['dir'] ?? 'desc';

function keepQueryN(array $filters, int $page): string {
  $params = [
    'read' => $filters['read'] ?? '',
    'q' => $filters['q'] ?? '',
    'sort' => $filters['sort'] ?? 'date',
    'dir' => $filters['dir'] ?? 'desc',
    'page' => $page,
  ];
  $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
  return http_build_query($params);
}
?>

<section class="container section">
  <h1>Notifications</h1>
  <p class="muted"><?= (int)$total ?> notification(s)</p>

  <form method="get" class="filters-bar">
    <input type="text" name="q" value="<?= Helpers::e($q) ?>" placeholder="Rechercher...">

    <select name="read">
      <option value="">Toutes</option>
      <option value="0" <?= $read==='0'?'selected':'' ?>>Non lues</option>
      <option value="1" <?= $read==='1'?'selected':'' ?>>Lues</option>
    </select>

    <select name="sort">
      <option value="date" <?= $sort==='date'?'selected':'' ?>>Date</option>
      <option value="type" <?= $sort==='type'?'selected':'' ?>>Type</option>
      <option value="title" <?= $sort==='title'?'selected':'' ?>>Titre</option>
      <option value="read" <?= $sort==='read'?'selected':'' ?>>Lu</option>
    </select>

    <select name="dir">
      <option value="desc" <?= $dir==='desc'?'selected':'' ?>>Desc</option>
      <option value="asc" <?= $dir==='asc'?'selected':'' ?>>Asc</option>
    </select>

    <button class="btn btn-outline" type="submit">Appliquer</button>
    <a class="btn" href="<?= Helpers::url('/notifications') ?>">Réinitialiser</a>
  </form>

  <form method="post" action="<?= Helpers::url('/notification/lire-tout') ?>" style="margin: 12px 0;">
    <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
    <button class="btn btn-outline" type="submit">Marquer tout comme lu</button>
  </form>

  <?php if (empty($items)): ?>
    <p class="muted">Aucune notification.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Titre</th>
            <th>Type</th>
            <th>Lu</th>
          </tr>
        </thead>
        <tbody>
	          <?php foreach ($items as $n): ?>
	            <?php
	              $title = trim((string)($n['message'] ?? ''));
	              if ($title === '') $title = '(Sans titre)';

	              $rawLink = (string)($n['link'] ?? '');
	              $href = '';
	              if ($rawLink !== '') {
	                $href = (str_starts_with($rawLink, 'http://') || str_starts_with($rawLink, 'https://'))
	                  ? $rawLink
	                  : Helpers::url($rawLink);
	              }
	            ?>
	            <tr>
	              <td><?= Helpers::e(date('d/m/Y H:i', strtotime((string)$n['created_at']))) ?></td>
	              <td>
	                <?php if ($href !== ''): ?>
	                  <a href="<?= Helpers::e($href) ?>"><?= Helpers::e($title) ?></a>
	                <?php else: ?>
	                  <?= Helpers::e($title) ?>
	                <?php endif; ?>
	              </td>
	              <td><?= Helpers::e((string)($n['type'] ?? '')) ?></td>
	              <td><?= (int)($n['is_read'] ?? 0) === 1 ? 'Oui' : 'Non' ?></td>
	            </tr>
	          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ((int)$totalPages > 1): ?>
      <nav class="pagination">
        <?php for ($p=1; $p <= (int)$totalPages; $p++): ?>
          <a class="<?= $p === (int)$page ? 'active' : '' ?>" href="<?= Helpers::url('/notifications?' . keepQueryN($filters, $p)) ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
