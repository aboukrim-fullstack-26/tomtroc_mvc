<?php
use App\Core\Helpers;

$isEdit = ($mode ?? '') === 'edit';
$action = $isEdit ? Helpers::url('/livre/editer?id=' . (int)(is_array($book) ? ($book['id'] ?? 0) : 0)) : Helpers::url('/livre/creer');
$title = $isEdit ? 'Modifier les informations' : 'Ajouter un livre';


// --- Photo: upload OU URL ---
// Par défaut: upload. Si le livre a déjà une URL externe, on pré-sélectionne "url".
$existingPhoto = is_array($book) ? (string)($book['photo_path'] ?? '') : '';
$defaultSource = ($existingPhoto !== '' && str_starts_with($existingPhoto, 'http')) ? 'url' : 'upload';
$photoSource = (string)($_POST['photo_source'] ?? $defaultSource);
$photoUrl    = (string)($_POST['photo_url'] ?? ($defaultSource === 'url' ? $existingPhoto : ''));

// Source pour l'aperçu
if ($existingPhoto && str_starts_with($existingPhoto, 'http')) {
  $imgSrc = $existingPhoto;
} elseif ($existingPhoto) {
  $imgSrc = BASE_URL . '/' . ltrim($existingPhoto, '/');
} else {
  $imgSrc = BASE_URL . '/assets/img/book-placeholder-wide.jpg';
}
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
    <input type="hidden" name="photo_keep" value="<?= Helpers::e(is_array($book) ? ($book['photo_path'] ?? '') : '') ?>">

    <div class="edit-grid">
      <div class="photo-block">
        <div class="photo-label">Photo</div>
        <img class="big-photo" src="<?= Helpers::e($imgSrc) ?>" alt="">

        <!-- Choix de la source (Upload ou URL) -->
        <div class="photo-source" style="display:flex; gap:14px; align-items:center; margin-top:10px;">
          <label style="display:flex; gap:8px; align-items:center;">
            <input type="radio" name="photo_source" value="upload" <?= ($photoSource === 'upload') ? 'checked' : '' ?>>
            Upload
          </label>
          <label style="display:flex; gap:8px; align-items:center;">
            <input type="radio" name="photo_source" value="url" <?= ($photoSource === 'url') ? 'checked' : '' ?>>
            URL
          </label>
        </div>

        <!-- Upload -->
        <div id="photo-upload-box" style="margin-top:10px; <?= ($photoSource === 'upload') ? '' : 'display:none;' ?>">
          <label class="small-link">
            Choisir un fichier
            <input type="file" name="photo" accept="image/*" hidden>
          </label>
          <div class="muted" style="margin-top:6px;">jpg / png / webp • max 2 Mo</div>
        
          <div id="photo-preview" style="margin-top:10px;">
            <img id="photo-preview-img" src="<?= Helpers::e($imgSrc) ?>" 
                 style="max-width:120px; max-height:160px; border-radius:6px; border:1px solid #ddd;">
          </div>

        <!-- URL -->
        <div id="photo-url-box" style="margin-top:10px; <?= ($photoSource === 'url') ? '' : 'display:none;' ?>">
          <input type="text" name="photo_url" placeholder="https://..." value="<?= Helpers::e($photoUrl) ?>">
          <div class="muted" style="margin-top:6px;">Lien direct vers une image (http/https) • .jpg .png .webp</div>
        </div>

        <script>
          (function(){
            const uploadBox = document.getElementById('photo-upload-box');
            const urlBox = document.getElementById('photo-url-box');
            document.querySelectorAll('input[name="photo_source"]').forEach(r => {
              r.addEventListener('change', function(){
                const v = document.querySelector('input[name="photo_source"]:checked')?.value || 'upload';
                uploadBox.style.display = (v === 'upload') ? 'block' : 'none';
                urlBox.style.display = (v === 'url') ? 'block' : 'none';
              });
            });
          
            // Preview upload image
            const fileInput = document.querySelector('input[name="photo"]');
            const urlInput = document.querySelector('input[name="photo_url"]');
            const previewImg = document.getElementById('photo-preview-img');

            if (fileInput) {
              fileInput.addEventListener('change', function(){
                const file = this.files[0];
                if (file) {
                  const reader = new FileReader();
                  reader.onload = e => previewImg.src = e.target.result;
                  reader.readAsDataURL(file);
                }
              });
            }

            if (urlInput) {
              urlInput.addEventListener('input', function(){
                if (this.value.startsWith('http')) {
                  previewImg.src = this.value;
                }
              });
            }

})();
        </script>
      </div></div>

      <div class="fields">
        <label>
          <span>Titre</span>
          <input type="text" name="title" value="<?= Helpers::e(is_array($book) ? ($book['title'] ?? '') : '') ?>" required>
        </label>

        <label>
          <span>Auteur</span>
          <input type="text" name="author" value="<?= Helpers::e(is_array($book) ? ($book['author'] ?? '') : '') ?>" required>
        </label>

        <label>
          <span>Commentaire</span>
          <textarea name="description" rows="10"><?= Helpers::e(is_array($book) ? ($book['description'] ?? '') : '') ?></textarea>
        </label>

        <label>
          <span>Disponibilité</span>
          <select name="status">
            <option value="available" <?= ((is_array($book) ? ($book['status'] ?? 'available') : 'available') === 'available') ? 'selected' : '' ?>>disponible</option>
            <option value="unavailable" <?= ((is_array($book) ? ($book['status'] ?? 'available') : 'available') === 'unavailable') ? 'selected' : '' ?>>non dispo.</option>
          </select>
        </label>

        <button class="btn" type="submit">Valider</button>
      </div>
    </div>
  </form>
</section>
