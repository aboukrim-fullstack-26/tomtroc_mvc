<?php
/**
 * app/Views/home/index.php
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
use App\Core\Helpers;
?>
<section class="hero container">
  <div class="hero-text">
    <h1>Rejoignez nos<br>lecteurs passionnés</h1>
    <p>
      Donnez une nouvelle vie à vos livres en les échangeant avec d'autres amoureux de la lecture.
      Nous croyons en la magie du partage de connaissances et d'histoires à travers les livres.
    </p>
    <a class="btn" href="<?= Helpers::url('/livres') ?>">Découvrir</a>
  </div>
  <div class="hero-image">
    <img src="<?= Helpers::url('/assets/img/hero.jpg') ?>" alt="Livres">
    <small class="credit">Hamza</small>
  </div>
</section>

<section class="container section">
  <h2 class="center">Les derniers livres ajoutés</h2>

  <div class="grid books-grid">
    <?php foreach ($latest as $b): ?>
      <a class="card book-card" href="<?= Helpers::url('/livre?id=' . (int)$b['id']) ?>">
        <div class="thumb">
          <?php
			$photo = $b['photo_path'];

			if ($photo && str_starts_with($photo, 'http')) {
				$src = $photo; // URL externe
			} elseif ($photo) {
				$src = BASE_URL . '/' . $photo; // fichier local
			} else {
				$src = BASE_URL . '/assets/img/book-placeholder.jpg';
			}
			?>

<img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($b['title']) ?>">
        </div>
        <div class="card-body">
          <div class="title"><?= Helpers::e($b['title']) ?></div>
          <div class="muted"><?= Helpers::e($b['author']) ?></div>
          <div class="small muted">Vendu par : <?= Helpers::e($b['owner_pseudo']) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="center">
    <a class="btn" href="<?= Helpers::url('/livres') ?>">Voir tous les livres</a>
  </div>
</section>

<section class="container section">
  <h2 class="center">Comment ça marche ?</h2>
  <p class="center muted">Échanger des livres avec TomTroc c'est simple et amusant ! Suivez ces étapes pour commencer :</p>
  <div class="steps">
    <div class="step">Inscrivez-vous gratuitement sur notre plateforme.</div>
    <div class="step">Ajoutez les livres que vous souhaitez échanger à votre profil.</div>
    <div class="step">Parcourez les livres disponibles chez d'autres membres.</div>
    <div class="step">Proposez un échange et discutez avec d'autres passionnés de lecture.</div>
  </div>
  <div class="center">
    <a class="btn outline" href="<?= Helpers::url('/livres') ?>">Voir tous les livres</a>
  </div>
</section>

<section class="values">
  <img class="values-banner" src="<?= Helpers::url('/assets/img/values.jpg') ?>" alt="">
  <div class="container values-content">
    <div>
      <h2>Nos valeurs</h2>
      <p>
      Chez Tom Troc, nous mettons l'accent sur le partage, la découverte et la communauté. Nos valeurs
      sont ancrées dans notre passion pour les livres et notre désir de créer des liens entre les
      lecteurs. Nous croyons en la puissance des histoires pour rassembler les gens et inspirer des
      conversations enrichissantes.
      </p>
      <p>
      Notre association a été fondée avec une conviction profonde : chaque livre mérite d'être lu et partagé.
      </p>
      <p>
      Nous sommes passionnés par la création d'une plateforme conviviale qui permet aux lecteurs de se connecter,
      de partager leurs découvertes littéraires et d'échanger des livres qui attendent patiemment sur les étagères.
      </p>
      <div class="signature">L’équipe Tom Troc</div>
    </div>
    <div class="values-sign" aria-hidden="true">
      <svg viewBox="0 0 200 140" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M100 118c-27 0-47-19-60-35C18 60 18 39 33 28c15-11 35-7 49 7 7 7 12 15 18 24 6-9 11-17 18-24 14-14 34-18 49-7 15 11 15 32-7 55-13 16-33 35-60 35z" stroke="#0aa66a" stroke-width="4"/>
        <path d="M130 128c-19 9-39 12-60 8" stroke="#0aa66a" stroke-width="4" stroke-linecap="round"/>
      </svg>
    </div>
  </div>
</section>
