<?php
use App\Core\Helpers;
?>
<section class="container section">
  <h1>Mes signalements</h1>

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
             href="<?= Helpers::url('/mes-signalements?page=' . $p) ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>

  <?php endif; ?>
</section>
