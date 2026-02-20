<?php
use App\Core\Helpers;
?>
<section class="container section">
  <h1>Mes signalements</h1>

  <?php
    $filters = $filters ?? ['q'=>'','status'=>'','type'=>'','reason'=>'','sort'=>'date','dir'=>'desc'];
  ?>
  <form class="search" method="get" action="<?= Helpers::url('/mes-signalements') ?>" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin:12px 0 14px 0;">
    <input type="text" name="q" placeholder="Rechercher..." value="<?= Helpers::e($filters['q'] ?? '') ?>">

    <select name="type" aria-label="Type">
      <option value="">Type : tous</option>
      <option value="book" <?= (($filters['type'] ?? '')==='book')?'selected':'' ?>>Livre</option>
      <option value="message" <?= (($filters['type'] ?? '')==='message')?'selected':'' ?>>Message</option>
    </select>

    <select name="status" aria-label="Statut">
      <option value="">Statut : tous</option>
      <option value="open" <?= (($filters['status'] ?? '')==='open')?'selected':'' ?>>Ouvert</option>
      <option value="reviewed" <?= (($filters['status'] ?? '')==='reviewed')?'selected':'' ?>>En revue</option>
      <option value="closed" <?= (($filters['status'] ?? '')==='closed')?'selected':'' ?>>Clos</option>
    </select>

    <select name="reason" aria-label="Motif">
      <option value="">Motif : tous</option>
      <?php foreach (($reasons ?? []) as $key => $label): ?>
        <option value="<?= Helpers::e($key) ?>" <?= (($filters['reason'] ?? '')===$key)?'selected':'' ?>><?= Helpers::e($label) ?></option>
      <?php endforeach; ?>
    </select>

    <select name="sort" aria-label="Trier">
      <option value="date" <?= (($filters['sort'] ?? 'date')==='date')?'selected':'' ?>>Date</option>
      <option value="type" <?= (($filters['sort'] ?? '')==='type')?'selected':'' ?>>Type</option>
      <option value="reason" <?= (($filters['sort'] ?? '')==='reason')?'selected':'' ?>>Motif</option>
      <option value="status" <?= (($filters['sort'] ?? '')==='status')?'selected':'' ?>>Statut</option>
    </select>

    <select name="dir" aria-label="Ordre">
      <option value="desc" <?= (($filters['dir'] ?? 'desc')==='desc')?'selected':'' ?>>↓</option>
      <option value="asc" <?= (($filters['dir'] ?? '')==='asc')?'selected':'' ?>>↑</option>
    </select>

    <button class="btn outline" type="submit">Appliquer</button>
    <a class="btn" href="<?= Helpers::url('/mes-signalements') ?>">Tout afficher</a>
  </form>


  <?php if ((int)$total === 0): ?>
    <p class="muted">Vous n’avez encore effectué aucun signalement.</p>
    <p><a class="btn" href="<?= Helpers::url('/livres') ?>">Retour aux livres</a></p>
  <?php else: ?>
    <p class="muted"><?= (int)$total ?> signalement(s)</p>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Livre</th>
            <th>Motif</th>
            <th>Statut</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reports as $r): ?>
            <tr>
              <td><?= Helpers::e(date('d/m/Y H:i', strtotime((string)$r['created_at']))) ?></td>
              <td>
                <?php if ($r['target_type'] === 'book'): ?>
                  <div><a href="<?= Helpers::url('/livre?id='.(int)$r['target_id']) ?>"><?= Helpers::e($r['book_title'] ?: ('#'.$r['target_id'])) ?></a></div>
                  <div class="muted"><?= Helpers::e($r['book_author'] ?: '') ?></div>
                <?php else: ?>
                  #<?= (int)$r['target_id'] ?>
                <?php endif; ?>
              </td>
              <td><?= Helpers::e($reasons[$r['reason']] ?? $r['reason']) ?></td>
              <td><span class="badge"><?= Helpers::e($r['status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination">
        <?php for ($p=1; $p <= $totalPages; $p++): ?>
          <a class="<?= $p === (int)$page ? 'active' : '' ?>"
             href="<?php $qs = $_GET; $qs['page']=$p; echo Helpers::url('/mes-signalements?' . http_build_query($qs)); ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>

  <?php endif; ?>
</section>
