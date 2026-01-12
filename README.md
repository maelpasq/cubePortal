# Cube Portal

Cube Portal est une application web développée par pamalstudio.fr. Elle centralise l'acces au portail Cube et propose une interface simple pour naviguer dans les fonctionnalites cles.

## Apercu

Cube Portal propose une base solide pour authentifier les utilisateurs, afficher un tableau de bord et gerer un espace d'administration. Le projet est structure autour de pages PHP simples, de templates et d'une base de donnees MySQL.

## Fonctionnalites

- Page d'accueil et parcours de connexion.
- Tableau de bord utilisateur.
- Zone d'administration.
- Sessions securisees et protection CSRF.

## Stack technique

- PHP avec PDO.
- MySQL / MariaDB.
- Templates PHP et assets statiques.

## Structure du projet

- `index.php` : routeur minimal.
- `bootstrap.php` : initialisation, sessions, connexion BDD.
- `pages/` : pages principales.
- `templates/` : elements d'interface partages.
- `lib/` : helpers, auth, CSRF.
- `assets/` : styles et ressources.
- `schema.sql` : schema de base de donnees.

## Installation locale

1. Cloner le depot.
2. Creer un fichier `config.local.php` a partir de `config.php` (ignore par Git) et definir les acces BDD.
3. Importer `schema.sql` dans votre base.
4. Lancer un serveur local, par exemple :

```bash
php -S localhost:8000
```

Puis ouvrir `http://localhost:8000`.

## Deploiement

Un workflow GitHub Actions (`.github/workflows/deploy.yml`) deploie sur Apache via SSH. Les secrets attendus :

- `SSH_HOST`, `SSH_USER`, `SSH_PORT`, `SSH_KEY`
- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`

## Licence

Licence non specifiee.

## Contact

Pamalstudio : https://pamalstudio.fr
