<?php
use App\Core\Auth;
use App\Core\Helpers;

$created = new DateTime($user['created_at']);
$now = new DateTime();
$diff = $created->diff($now);
$since = $diff->y >= 1 ? ('Membre depuis ' . $diff->y . ' an' . ($diff->y > 1 ? 's' : '')) : 'Nouveau membre';
?>
<section class="container section">
  <div class="public-grid">
    <div class="card profile-card">
      <img class="profile-avatar" src="<?= Helpers::url($user['avatar_path'] ?: '/assets/img/avatar-placeholder.jpg') ?>" alt="">
      <div class="divider"></div>
      <div class="profile-name"><?= Helpers::e($user['pseudo']) ?></div>
      <div class="muted"><?= Helpers::e($since) ?></div>
      <div class="tiny-title">BIBLIOTHEQUE</div>
      <div class="muted"><?= count($books) ?> livres</div>

      <?php if (Auth::check() && (int)$user['id'] !== Auth::id()): ?>
        <form method="post" action="<?= Helpers::url('/message/nouveau') ?>">
          <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
          <input type="hidden" name="to_user_id" value="<?= (int)$user['id'] ?>">
          <input type="hidden" name="body" value="Bonjour <?= Helpers::e($user['pseudo']) ?> !">
          <button class="btn outline full" type="submit">Écrire un message</button>
        </form>
      <?php endif; ?>
    </div>

    <div class="card table-card">
      <table class="table">
        <thead>
          <tr><th>Photo</th><th>Titre</th><th>Auteur</th><th>Description</th></tr>
        </thead>
        <tbody>
          <?php foreach ($books as $b): ?>
          <tr>
            <?php
            $photo = $b['photo_path'] ?? '';
            if ($photo && str_starts_with($photo, 'http')) {
                $src = $photo;
            } elseif ($photo) {
                $src = BASE_URL . '/' . ltrim($photo, '/');
            } else {
                $src = BASE_URL . '/assets/img/book-placeholder.jpg';
            }
          ?>
          <td><img class="table-thumb" src="<?= Helpers::e($src) ?>" alt=""></td>
            <td><a href="<?= Helpers::url('/livre?id=' . (int)$b['id']) ?>"><?= Helpers::e($b['title']) ?></a></td>
            <td><?= Helpers::e($b['author']) ?></td>
            <td class="italic"><?= Helpers::e(mb_strimwidth($b['description'] ?? '', 0, 110, '…')) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
