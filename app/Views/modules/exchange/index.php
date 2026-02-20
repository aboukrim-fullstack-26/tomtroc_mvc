<?php
use App\Core\Helpers;

$status = $filters['status'] ?? '';
$q = $filters['q'] ?? '';
$sort = $filters['sort'] ?? 'date';
$dir = $filters['dir'] ?? 'desc';
$box = $filters['box'] ?? 'all';

function keepQuery(array $filters, int $page): string {
  $params = [
    'status' => $filters['status'] ?? '',
    'q' => $filters['q'] ?? '',
    'sort' => $filters['sort'] ?? 'date',
    'dir' => $filters['dir'] ?? 'desc',
    'box' => $filters['box'] ?? 'all',
    'page' => $page,
  ];
  $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
  return http_build_query($params);
}
?>

<section class="container section">
  <h1>Demandes d'échange</h1>
  <p class="muted"><?= (int)$total ?> demande(s)</p>

  <form method="get" class="filters-bar">
    <input type="text" name="q" value="<?= Helpers::e($q) ?>" placeholder="Rechercher (titre ou pseudo)">

    <select name="status">
      <option value="">Tous statuts</option>
      <option value="pending" <?= $status==='pending'?'selected':'' ?>>En attente</option>
      <option value="accepted" <?= $status==='accepted'?'selected':'' ?>>Acceptée</option>
      <option value="rejected" <?= $status==='rejected'?'selected':'' ?>>Refusée</option>
    </select>


    <select name="box">
      <option value="all" <?= $box==='all'?'selected':'' ?>>Toutes</option>
      <option value="sent" <?= $box==='sent'?'selected':'' ?>>Envoyées</option>
      <option value="received" <?= $box==='received'?'selected':'' ?>>Reçues</option>
    </select>

    <select name="sort">
      <option value="date" <?= $sort==='date'?'selected':'' ?>>Date</option>
      <option value="status" <?= $sort==='status'?'selected':'' ?>>Statut</option>
      <option value="book" <?= $sort==='book'?'selected':'' ?>>Livre</option>
      <option value="requester" <?= $sort==='requester'?'selected':'' ?>>Utilisateur</option>
    </select>

    <select name="dir">
      <option value="desc" <?= $dir==='desc'?'selected':'' ?>>Desc</option>
      <option value="asc" <?= $dir==='asc'?'selected':'' ?>>Asc</option>
    </select>

    <button class="btn btn-outline" type="submit">Appliquer</button>
    <a class="btn" href="<?= Helpers::url('/demandes') ?>">Réinitialiser</a>
  </form>

  <?php if (empty($items)): ?>
    <p class="muted">Aucune demande trouvée.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Livre</th>
            <th>Utilisateur</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= Helpers::e(date('d/m/Y H:i', strtotime((string)$it['created_at']))) ?></td>
              <td>
                <a href="<?= Helpers::url('/livre?id='.(int)$it['book_id']) ?>"><?= Helpers::e($it['book_title']) ?></a>
                <?php if (!empty($it['book_author'])): ?><div class="muted"><?= Helpers::e($it['book_author']) ?></div><?php endif; ?>
              </td>
              <td><?= Helpers::e($it['requester_pseudo']) ?></td>
              <td><span class="badge badge-outline"><?= Helpers::e($it['box'] ?? '') ?></span></td>
              <td><span class="badge"><?= Helpers::e($it['status']) ?></span></td>
              <td>
                <?php if (($it['status'] === 'pending') && (($it['box'] ?? '') === 'received')): ?>
                  <form method="post" action="<?= Helpers::url('/demande/accepter') ?>" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                    <button class="btn btn-outline" type="submit">Accepter</button>
                  </form>
                  <form method="post" action="<?= Helpers::url('/demande/refuser') ?>" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                    <button class="btn" type="submit">Refuser</button>
                  </form>
                <?php else: ?>
                  <span class="muted">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ((int)$totalPages > 1): ?>
      <nav class="pagination">
        <?php for ($p=1; $p <= (int)$totalPages; $p++): ?>
          <a class="<?= $p === (int)$page ? 'active' : '' ?>" href="<?= Helpers::url('/demandes?' . keepQuery($filters, $p)) ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
