# TomTroc (PHP MVC, POO, sans librairies)

## Prérequis
- PHP >= 8.1
- MySQL / MariaDB
- Apache (recommandé) ou PHP built-in server

## Installation
1) Copier la config :
```bash
Le fichier config/config.php est déjà fourni.
```
2) Renseigner vos identifiants DB dans `config/config.php`

3) Créer la base et les tables :
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p tomtroc < database/seed.sql
```

4) Démarrer le serveur :
- Apache : document root sur `public/` (et activer `mod_rewrite`)
- ou PHP built-in :
```bash
php -S localhost:8000 -t public
```

## Routes principales
- `/` accueil
- `/livres` liste des livres
- `/livre?id=1` fiche livre
- `/inscription` / `/connexion` / `/deconnexion`
- `/mon-compte`
- `/livre/editer?id=1` (ou `/livre/creer`)
- `/profil?id=2` profil public
- `/messagerie` (liste + conversation)

## Sécurité
- Mots de passe hashés (password_hash)
- Requêtes préparées (PDO)
- CSRF sur les formulaires
- Échappement HTML (XSS)
