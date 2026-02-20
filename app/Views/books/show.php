<?php
/**
 * app/Views/books/show.php
 * Détail d'un livre + actions modulaires (Favoris / Signalement / Message)
 *
 * @author @aboukrim
 */

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Core\ModuleManager;
use App\Modules\Exchange\Models\ExchangeRequest;

$modules = new ModuleManager();


$pendingExchange = null;
$exchangeHistory = [];

// Module Exchange : on prépare les infos métier du livre
if (Auth::check() && $modules->isEnabled('Exchange') && isset($book['id'])) {
  $pendingExchange = ExchangeRequest::pendingForRequesterAndBook(Auth::id(), (int)$book['id']);
  $exchangeHistory = ExchangeRequest::historyForBook((int)$book['id'], Auth::id());
}


// Image
$photo = $book['photo_path'] ?? '';
if ($photo && str_starts_with($photo, 'http')) {
  $src = $photo;
} elseif ($photo) {
  $src = BASE_URL . '/' . $photo;
} else {
  $src = BASE_URL . '/assets/img/book-placeholder.jpg';
}

// Favoris : état
$isFavorite = false;
if (Auth::check() && $modules->isEnabled('Favorites') && class_exists(\App\Modules\Favorites\Models\Favorite::class)) {
  $isFavorite = \App\Modules\Favorites\Models\Favorite::exists(Auth::id(), (int)$book['id']);
}

$csrf = $csrf ?? Csrf::token();

// Ratings : stats + note user (si module activé)
$ratingStats = ['avg' => 0.0, 'cnt' => 0];
$userRating = null;
if ($modules->isEnabled('Ratings') && class_exists(\App\Modules\Ratings\Models\Rating::class)) {
  $ratingStats = \App\Modules\Ratings\Models\Rating::statsForBook((int)$book['id']);
  if (Auth::check()) {
    $userRating = \App\Modules\Ratings\Models\Rating::userRating(Auth::id(), (int)$book['id']);
  }
}
?>

<section class="book-hero book-single">
  <div class="book-cover">
    <img class="book-cover__img" src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
  </div>

  <div class="book-detail">
    <div class="book-detail__inner">
      <h1><?= Helpers::e($book['title']) ?></h1>
      <div class="muted">par <?= Helpers::e($book['author']) ?></div>

      <div class="separator"></div>

      <h3 class="tiny-title">DESCRIPTION</h3>
      <p class="book-desc"><?= nl2br(Helpers::e($book['description'])) ?></p>

      <h3 class="tiny-title">PROPRIÉTAIRE</h3>
      <div class="owner">
        <img class="avatar" src="<?= Helpers::url($owner['avatar_path'] ?: '/assets/img/avatar-placeholder.jpg') ?>" alt="">
        <a href="<?= Helpers::url('/profil?id=' . (int)$owner['id']) ?>"><?= Helpers::e($owner['pseudo']) ?></a>
      </div>

      <div class="separator"></div>

      <div class="book-actions">
        <?php if (Auth::check() && $modules->isEnabled('Favorites')): ?>
          <?php if (!$isFavorite): ?>
            <form method="post" action="<?= Helpers::url('/favori/ajouter') ?>" class="fav-form">
              <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
              <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
              <input type="hidden" name="back" value="<?= Helpers::e($_SERVER['REQUEST_URI'] ?? '/livres') ?>">
              <button class="fav-btn" type="submit" title="Ajouter aux favoris" aria-label="Ajouter aux favoris">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M12 17.27l-5.18 3.05 1.64-5.81L3 9.24l6-.52L12 3l3 5.72 6 .52-4.46 5.27 1.64 5.81L12 17.27z"/>
                </svg>
              </button>
            </form>
          <?php else: ?>
            <form method="post" action="<?= Helpers::url('/favori/supprimer') ?>" class="fav-form">
              <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
              <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
              <input type="hidden" name="back" value="<?= Helpers::e($_SERVER['REQUEST_URI'] ?? '/livres') ?>">
              <button class="fav-btn is-active" type="submit" title="Retirer des favoris" aria-label="Retirer des favoris">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M12 17.27l-5.18 3.05 1.64-5.81L3 9.24l6-.52L12 3l3 5.72 6 .52-4.46 5.27 1.64 5.81L12 17.27z"/>
                </svg>
              </button>
            </form>
          <?php endif; ?>
        <?php endif; ?>

  <?php if (Auth::check() && $modules->isEnabled('Exchange') && (int)$owner['id'] !== Auth::id()): ?>
    <?php if ($pendingExchange): ?>
      <div class="alert alert-info exchange-info">
        ✅ Demande d’échange déjà envoyée — <strong>en attente</strong>.
      </div>
    <?php else: ?>
      <form method="post" action="<?= Helpers::url('/demande/creer') ?>" class="exchange-cta">
        <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
        <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
        <input type="hidden" name="message" value="Bonjour, je souhaite échanger votre livre &quot;<?= Helpers::e($book['title']) ?>&quot;.">
        <input type="hidden" name="back" value="<?= Helpers::e($_SERVER['REQUEST_URI'] ?? '/livres') ?>">
        <button class="btn btn-outline" type="submit">Demander un échange</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (Auth::check() && $modules->isEnabled('Exchange') && !empty($exchangeHistory)): ?>
    <div class="exchange-history-block">
      <h3 class="tiny-title">HISTORIQUE DES ÉCHANGES (CE LIVRE)</h3>
      <div class="exchange-history-grid">
        <?php foreach ($exchangeHistory as $ex): ?>
          <div class="exchange-history-item">
            <div class="exchange-history-top">
              <div class="exchange-history-user">
                <strong><?= Helpers::e($ex['requester_pseudo'] ?? 'Utilisateur') ?></strong>
                <span class="badge badge-outline"><?= Helpers::e($ex['status'] ?? '') ?></span>
              </div>
              <div class="muted"><?= Helpers::e(date('d/m/Y H:i', strtotime((string)($ex['created_at'] ?? '')))) ?></div>
            </div>
            <?php if (!empty($ex['message'])): ?>
              <div class="muted exchange-history-msg"><?= nl2br(Helpers::e($ex['message'])) ?></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>


<?php if (Auth::check() && (int)$owner['id'] !== Auth::id() && $modules->isEnabled('Message')): ?>
          <form method="post" action="<?= Helpers::url('/message/nouveau') ?>" class="message-cta">
            <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
            <input type="hidden" name="to_user_id" value="<?= (int)$owner['id'] ?>">
            <input type="hidden" name="body" value="Bonjour, je suis intéressé(e) par votre livre &quot;<?= Helpers::e($book['title']) ?>&quot;.">
            <button class="btn full" type="submit">Envoyer un message</button>
          </form>
        <?php elseif (!Auth::check() && $modules->isEnabled('Message')): ?>
          <p class="center"><a class="btn" href="<?= Helpers::url('/connexion') ?>">Connectez-vous pour envoyer un message</a></p>
        <?php endif; ?>
      </div>

      <?php if ($modules->isEnabled('Ratings')): ?>
        <div class="rating-block">
          <div class="tiny-title">ÉVALUATION</div>
          <div class="rating-summary">
            <div class="stars" aria-label="Note moyenne">
              <?php
                $avg = (float)$ratingStats['avg'];
                $full = (int)floor($avg + 0.00001);
                for ($i = 1; $i <= 5; $i++):
              ?>
                <span class="star <?= $i <= $full ? 'is-on' : '' ?>" aria-hidden="true">★</span>
              <?php endfor; ?>
            </div>
            <div class="muted">
              <?= number_format($avg, 1) ?> / 5
              <span class="muted">(<?= (int)$ratingStats['cnt'] ?> avis)</span>
            </div>
          </div>

          <?php if (Auth::check()): ?>
            <form class="rating-form" method="post" action="<?= Helpers::url('/note') ?>">
              <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
              <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">

              <div class="rating-choices" aria-label="Donner une note">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                  <input type="radio" id="rate-<?= $i ?>" name="rating" value="<?= $i ?>" <?= ($userRating === $i) ? 'checked' : '' ?> required>
                  <label for="rate-<?= $i ?>" title="<?= $i ?>">★</label>
                <?php endfor; ?>
              </div>

              <button class="btn btn-outline" type="submit">Noter</button>
            </form>
          <?php else: ?>
            <p class="muted">Connectez-vous pour noter ce livre.</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if (Auth::check() && $modules->isEnabled('Reports')): ?>
        <form method="post" action="<?= Helpers::url('/signalement/livre') ?>" class="report-form">
          <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
          <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
          <input type="hidden" name="back" value="<?= Helpers::e($_SERVER['REQUEST_URI'] ?? '/livres') ?>">

          <label class="tiny-title">SIGNALER CE LIVRE</label>
          <select name="reason" required>
            <option value="spam">Spam</option>
            <option value="fake">Informations fausses</option>
            <option value="offensive">Contenu offensant</option>
            <option value="copyright">Droits d'auteur</option>
            <option value="other">Autre</option>
          </select>

          <textarea name="comment" rows="3" placeholder="Expliquez brièvement (optionnel)"></textarea>
          <button class="btn btn-outline" type="submit">Envoyer le signalement</button>
        </form>
      <?php endif; ?>

      <?php if (Auth::check() && !$modules->isEnabled('Message') && (int)$owner['id'] !== Auth::id()): ?>
        <p class="muted">Messagerie désactivée (module Message OFF).</p>
      <?php endif; ?>
    </div>
  </div>
</section>
