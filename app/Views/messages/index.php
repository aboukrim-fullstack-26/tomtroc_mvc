<?php
/**
 * app/Views/messages/index.php
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
?>
<section class="container section messaging">
  <div class="msg-grid">
    <aside class="msg-list">
      <h1>Messagerie</h1>
      <?php foreach ($conversations as $c): ?>
        <a class="msg-item <?= ($active && (int)$active['id'] === (int)$c['id']) ? 'active' : '' ?>"
           href="<?= Helpers::url('/messagerie?c=' . (int)$c['id']) ?>">
          <img class="avatar" src="<?= Helpers::url($c['peer_avatar'] ?: '/assets/img/avatar-placeholder.jpg') ?>" alt="">
          <div class="msg-meta">
            <div class="row">
              <strong><?= Helpers::e($c['peer_pseudo']) ?></strong>
              <span class="time"><?= Helpers::e($c['last_at'] ? date('d.m H:i', strtotime($c['last_at'])) : '') ?></span>
            </div>
            <div class="snippet"><?= Helpers::e(mb_strimwidth($c['last_body'] ?? '', 0, 30, '…')) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </aside>

    <section class="msg-thread">
      <?php if (!$active): ?>
        <p class="muted">Aucune conversation.</p>
      <?php else: ?>
        <div class="thread-head">
          <img class="avatar" src="<?= Helpers::url($peer['avatar_path'] ?: '/assets/img/avatar-placeholder.jpg') ?>" alt="">
          <strong><?= Helpers::e($peer['pseudo']) ?></strong>
        </div>

        <div class="thread-body">
          <?php foreach ($messages as $m): ?>
            <?php $mine = (int)$m['sender_id'] === Auth::id(); ?>
            <div class="bubble-row <?= $mine ? 'mine' : 'theirs' ?>">
              <div class="bubble">
                <div class="bubble-time"><?= Helpers::e(date('d.m H:i', strtotime($m['created_at']))) ?></div>
                <div><?= nl2br(Helpers::e($m['body'])) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php
          // Pagination du fil (10 messages par page). Paramètre: mpage.
          $page = (int)($mpage ?? 1);
          $totalPages = (int)($mTotalPages ?? 1);
          $params = $_GET;
          unset($params['mpage']);
          $base = http_build_query($params);
          $base = $base ? $base . '&' : '';
        ?>
        <?php if ($totalPages > 1): ?>
          <div class="pagination pagination--messages">
            <?php if ($page > 1): ?>
              <a class="pagination-link" href="?<?= $base ?>mpage=<?= $page - 1 ?>">← Plus anciens</a>
            <?php endif; ?>
            <span class="pagination-info">Page <?= $page ?> / <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
              <a class="pagination-link" href="?<?= $base ?>mpage=<?= $page + 1 ?>">Plus récents →</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <form class="thread-form" method="post" action="<?= Helpers::url('/message/nouveau') ?>">
          <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
          <input type="hidden" name="conversation_id" value="<?= (int)$active['id'] ?>">
          <input class="input" type="text" name="body" placeholder="Tapez votre message ici" required>
          <button class="btn" type="submit">Envoyer</button>
        </form>
      <?php endif; ?>
    </section>
  </div>
</section>


