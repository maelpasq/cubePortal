# Cube Portal — Minimal PHP SaaS

Installation rapide:

1. Importez `sql/init.sql` dans phpMyAdmin pour créer la base et la table `users`.
2. Éditez `config/.env.php` pour renseigner vos identifiants MySQL.
3. Placez ce dossier sous un hôte Apache + PHP (mod_rewrite activé).
4. Accédez à `http://yourhost/setup/create_admin.php` pour créer le premier compte administrateur.
5. Connectez-vous via `http://yourhost/login` puis accédez à `/dashboard`.

Sécurité et notes:
- Les mots de passe sont stockés avec `password_hash`.
- Les pages sont protégées par session et vérifications de rôle.
- Les formulaires critiques utilisent un jeton CSRF côté serveur.
- `.htaccess` tente de masquer les extensions `.php` et restreint l'accès au dossier `config`.

Ne laissez pas `config/.env.php` suivi par Git en production.
