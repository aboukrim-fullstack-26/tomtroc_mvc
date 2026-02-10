<?php
/**
 * app/Views/auth/login.php
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
<section class="container auth">
  <div class="auth-left">
    <h1>Connexion</h1>

    <?php if (!empty($errors)): ?>
      <div class="alert">
        <?php foreach ($errors as $e): ?>
          <div><?= Helpers::e($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="form">
      <input type="hidden" name="csrf_token" value="<?= Helpers::e($csrf) ?>">
      <label>
        <span>Adresse email</span>
        <input type="email" name="email" required>
      </label>

      <label>
        <span>Mot de passe</span>
        <input type="password" name="password" required>
      </label>

      <button class="btn" type="submit">Se connecter</button>
    </form>

    <p class="small">Pas de compte ? <a href="<?= Helpers::url('/inscription') ?>">Inscrivez-vous</a></p>
  </div>

  <div class="auth-right">
    <img src="<?= Helpers::url('/assets/img/auth.jpg') ?>" alt="">
  </div>
</section>
