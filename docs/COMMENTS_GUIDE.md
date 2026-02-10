# Guide de lecture des commentaires (TomTroc)

- **Auteur :** aboukrim  
- **Objectif :** faciliter le debug et les évolutions sans casser la V4 stable.

## Ordre d’exécution global (MVC)

1. `public/index.php` : point d’entrée HTTP (Front Controller)
2. `app/bootstrap.php` : charge config, autoload, session
3. `app/Core/Router.php` : lit l’URL, match une route
4. Controller : exécute la logique (validation, sécurité)
5. Model : accès DB / logique métier
6. View : rendu HTML via layout + vues

## Conventions ajoutées

- En-tête de fichier : rôle + ordre d’exécution + `@author aboukrim`
- Docblocks : ajoutés au-dessus des méthodes (sans modifier le code)
