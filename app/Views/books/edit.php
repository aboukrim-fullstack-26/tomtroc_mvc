<?php
use App\Core\Helpers;

$isEdit = ($mode ?? '') === 'edit';
$action = $isEdit ? Helpers::url('/livre/editer?id=' . (int)$book['id']) : Helpers::url('/livre/creer');
$title = $isEdit ? 'Modifier les informations' : 'Ajouter un livre';
?>
<section class="container section">
  <a class="back" href="<?= Helpers::url('/mon-compte') ?>">← retour</a>
  <h1><?= Helpers::e($title) ?></h1>

  <?php if (!empty($errors)): ?>
    <div class="alert">
      <?php foreach ($errors as $e): ?>
        <div><?= Helpers::e($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form class="edit-book" method="post" enctype="multipart/form-data" action="<?= $action ?>">
    <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
    <input type="hidden" name="photo_keep" value="<?= Helpers::e($book['photo_path'] ?? '') ?>">

    <div class="edit-grid">
      <div class="photo-block">
        <div class="photo-label">Photo</div>
        <img class="big-photo" src="<?= Helpers::url(($book['photo_path'] ?? '') ?: '/assets/img/book-placeholder-wide.jpg') ?>" alt="">
        <label class="small-link">
          Modifier la photo
          <input type="file" name="photo" accept="image/*" hidden>
        </label>
      </div>

      <div class="fields">
        <label>
          <span>Titre</span>
          <input type="text" name="title" value="<?= Helpers::e($book['title'] ?? '') ?>" required>
        </label>

        <label>
          <span>Auteur</span>
          <input type="text" name="author" value="<?= Helpers::e($book['author'] ?? '') ?>" required>
        </label>

        <label>
          <span>Commentaire</span>
          <textarea name="description" rows="10"><?= Helpers::e($book['description'] ?? '') ?></textarea>
        </label>

        <label>
          <span>Disponibilité</span>
          <select name="status">
            <option value="available" <?= (($book['status'] ?? 'available') === 'available') ? 'selected' : '' ?>>disponible</option>
            <option value="unavailable" <?= (($book['status'] ?? 'available') === 'unavailable') ? 'selected' : '' ?>>non dispo.</option>
          </select>
        </label>

        <button class="btn" type="submit">Valider</button>
      </div>
    </div>
  </form>
</section>
