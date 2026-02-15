# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

**Langue : toujours répondre en français.**

## Projet

Site vitrine de **Thalye d'Oriam** (thalyedoriam.fr), artiste peintre contemporaine. PHP simple sans framework, SQLite, vanilla JS/CSS.

## Commandes

```bash
# Lancer le serveur de développement
php -S localhost:8000 -t public/

# Vérifier la syntaxe PHP
for f in app/*.php templates/**/*.php public/index.php; do php -l "$f"; done
```

## Architecture

```
public/          → Document root Apache (index.php = point d'entrée unique + .htaccess)
app/             → Code métier (config, database SQLite, routeur, auth, fonctions utilitaires)
templates/       → Vues PHP (layout.php principal + pages/ publiques + admin/)
data/site.db     → Base SQLite créée automatiquement au premier accès
public/uploads/  → Images uploadées via l'admin (oeuvres/, expos/, boutique/)
```

### Routage

`app/router.php` — routeur simple basé sur `$_SERVER['REQUEST_URI']`. Les routes publiques sont dans un tableau `$routes`, les sections expositions/boutique sont conditionnées par les settings `expos_enabled` / `boutique_enabled`. Les routes admin utilisent des patterns regex pour le CRUD (`/admin/{section}/{id}/{action}`).

### Base de données

`app/database.php` — connexion SQLite via PDO (singleton `getDb()`). Tables : `settings` (key/value), `oeuvres`, `expositions`, `boutique`. Le schéma est créé automatiquement si `data/site.db` n'existe pas. Fonctions helpers : `getSetting()`, `setSetting()`, `getAllSettings()`.

### Templates

Le layout principal (`templates/layout.php`) wrape le contenu via `ob_start()` + variable `$content`. Les pages admin utilisent leur propre layout (`templates/admin/layout.php`) avec variable `$adminContent`. La variable `$settings` (tableau associatif de tous les réglages) est disponible dans tous les templates publics.

### Admin

Authentification par session PHP (`app/auth.php`). Un seul compte, mot de passe hashé stocké en setting `admin_password`. Mot de passe par défaut : `admin`. CSRF sur tous les formulaires via `csrfField()` / `verifyCsrf()`.

## Conventions

- Échappement HTML systématique avec `e()` (alias de `htmlspecialchars`)
- Upload d'images via `uploadImage($file, $subdir)` → retourne le chemin relatif
- Affichage d'images via `imageUrl($path)` → URL complète ou placeholder SVG
- Flash messages via `flashMessage()` / `getFlash()`
- `SITE_URL` défini dans `app/config.php` (env `SITE_URL` ou `http://localhost:8000`)
