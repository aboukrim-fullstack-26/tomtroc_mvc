<?php
use App\Core\Helpers;
use App\Core\Auth;

$created = new DateTime($user['created_at']);
$now = new DateTime();
$diff = $created->diff($now);
$since = $diff->y >= 1 ? ('Membre depuis ' . $diff->y . ' an' . ($diff->y > 1 ? 's' : '')) : 'Nouveau membre';
?>
<section class="container section">
  <h1>Mon compte</h1>

  <?php if (!empty($errors)): ?>
    <div class="alert">
      <?php foreach ($errors as $e): ?>
        <div><?= Helpers::e($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="account-grid">
    <div class="card profile-card">
      <img class="profile-avatar" src="<?= Helpers::url($user['avatar_path'] ?: '/assets/img/avatar-placeholder.jpg') ?>" alt="">
      <div class="small-link center">modifier</div>
      <div class="divider"></div>
      <div class="profile-name"><?= Helpers::e($user['pseudo']) ?></div>
      <div class="muted"><?= Helpers::e($since) ?></div>
      <div class="tiny-title">BIBLIOTHEQUE</div>
      <div class="muted"><?= count($books) ?> livres</div>
    </div>

    <div class="card info-card">
      <h3>Vos informations personnelles</h3>
      <form method="post" enctype="multipart/form-data" class="form">
        <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
        <input type="hidden" name="avatar_keep" value="<?= Helpers::e($user['avatar_path'] ?? '') ?>">

        <label>
          <span>Adresse email</span>
          <input type="email" name="email" value="<?= Helpers::e($user['email']) ?>" required>
        </label>

        <label>
          <span>Mot de passe</span>
          <input type="password" name="password" placeholder="Laisser vide pour ne pas changer">
        </label>

        <label>
          <span>Pseudo</span>
          <input type="text" name="pseudo" value="<?= Helpers::e($user['pseudo']) ?>" required>
        </label>

        <label class="small-link">
          Changer la photo de profil
          <input type="file" name="avatar" accept="image/*" hidden>
        </label>

        <button class="btn outline" type="submit">Enregistrer</button>
      </form>

      <div class="actions-row">
        <a class="btn" href="<?= Helpers::url('/livre/creer') ?>">Ajouter un livre</a>
      </div>
    </div>
  </div>

  <div class="card table-card">
    <div class="books-head" style="padding:16px 16px 0 16px;">
      <form class="search" method="get" action="<?= Helpers::url('/mon-compte') ?>">
        <input type="text" name="bq" placeholder="Rechercher dans ma bibliothèque" value="<?= Helpers::e($bq ?? '') ?>">

        <select name="bstatus" aria-label="Filtrer par disponibilité">
          <option value="all" <?= (($bstatus ?? 'all') === 'all') ? 'selected' : '' ?>>Tous</option>
          <option value="available" <?= (($bstatus ?? '') === 'available') ? 'selected' : '' ?>>Disponibles</option>
          <option value="unavailable" <?= (($bstatus ?? '') === 'unavailable') ? 'selected' : '' ?>>Indisponibles</option>
        </select>

        <select name="bsort" aria-label="Trier">
          <option value="created_desc" <?= (($bsort ?? 'created_desc') === 'created_desc') ? 'selected' : '' ?>>Plus récents</option>
          <option value="created_asc" <?= (($bsort ?? '') === 'created_asc') ? 'selected' : '' ?>>Plus anciens</option>
          <option value="title_asc" <?= (($bsort ?? '') === 'title_asc') ? 'selected' : '' ?>>Titre A→Z</option>
          <option value="title_desc" <?= (($bsort ?? '') === 'title_desc') ? 'selected' : '' ?>>Titre Z→A</option>
        </select>

        <button class="btn outline" type="submit">Appliquer</button>
        <a class="btn" href="<?= Helpers::url('/mon-compte') ?>">Tout afficher</a>
      </form>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>Photo</th><th>Titre</th><th>Auteur</th><th>Description</th><th>Disponibilité</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($books)): ?>
        <tr><td colspan="6" class="muted" style="padding:16px;">Aucun livre ne correspond à votre filtre.</td></tr>
      <?php else: ?>
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
          <td><?= Helpers::e($b['title']) ?></td>
          <td><?= Helpers::e($b['author']) ?></td>
          <td class="italic"><?= Helpers::e(mb_strimwidth($b['description'] ?? '', 0, 80, '…')) ?></td>
          <td>
            <?php if (($b['status'] ?? '') === 'available'): ?>
              <span class="pill green">disponible</span>
            <?php else: ?>
              <span class="pill red">non dispo.</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <a href="<?= Helpers::url('/livre/editer?id=' . (int)$b['id']) ?>">Éditer</a>
            <form method="post" action="<?= Helpers::url('/livre/supprimer') ?>" onsubmit="return confirm('Supprimer ce livre ?');">
              <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
              <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
              <button class="link red" type="submit">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  
	<?php if (($bTotalPages ?? 1) > 1): ?>
        <div class="pagination" style="display:flex; gap:8px; justify-content:center; margin-top:16px; flex-wrap:wrap;">
          <?php
            $qs = $_GET;
            for ($p = 1; $p <= (int)$bTotalPages; $p++):
              $qs['bpage'] = $p;
              $href = Helpers::url('/mon-compte?' . http_build_query($qs) . '#books' .$p);
          ?>
            <a class="btn <?= ($p === (int)($bpage ?? 1)) ? '' : 'outline' ?>" href="<?= $href ?>"><?= (int)$p ?></a>
          <?php endfor; ?>
        </div>
    <?php endif; ?>
	 
</div>
</section>