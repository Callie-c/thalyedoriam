# Déploiement thalyedoriam.fr

Remplacement du WordPress par le site PHP/SQLite (nathalie2).

**Serveur** : terred.kdrezo.net
**Compte** : nathalie
**Domaine** : thalyedoriam.fr

---

## 1. Prérequis serveur

Le serveur doit avoir :
- PHP 7.4+ avec extensions : `pdo_sqlite`, `mbstring`, `gd` (pour images)
- Apache avec `mod_rewrite` activé
- `AllowOverride All` sur le DocumentRoot (pour le .htaccess)

## 2. Accès SSH

### Créer/configurer la clé SSH

```bash
# Si pas de clé dédiée, ajouter dans ~/.ssh/config :
Host nathalie
    HostName terred.kdrezo.net
    User nathalie
    IdentityFile ~/.ssh/id_rsa
    ServerAliveInterval 60
```

Tester la connexion :
```bash
ssh nathalie "whoami"
```

### Monter le répertoire distant (SSHFS)

```bash
# Créer le point de montage si nécessaire
mkdir -p /D1/web/users/denis/nathalie/mnt

# Monter
sudo sshfs nathalie:~ /D1/web/users/denis/nathalie/mnt
```

## 3. Sauvegarder le WordPress actuel

Avant toute chose, sauvegarder le site WordPress :

```bash
# Sur le serveur (via SSH)
ssh nathalie "tar czf ~/backup-wordpress-$(date +%Y%m%d).tar.gz ~/www/"
```

Ou en local via le montage SSHFS :
```bash
cp -r /D1/web/users/denis/nathalie/mnt/www /D1/web/users/denis/nathalie/backup-wordpress
```

## 4. Initialiser le dépôt git

### En local (nathalie2)

```bash
cd /D1/web/users/denis/nathalie2
git init
git add -A
git commit -m "Initial commit - site Thalye d'Oriam PHP/SQLite"
```

### Sur le serveur (bare repo pour le deploy)

```bash
ssh nathalie "git init --bare ~/deploy.git"
```

### Hook post-receive (déploiement automatique)

```bash
ssh nathalie 'cat > ~/deploy.git/hooks/post-receive << '\''HOOK'\''
#!/bin/bash
TARGET="$HOME/www"
GIT_DIR="$HOME/deploy.git"

echo ">> Déploiement vers $TARGET..."
git --work-tree=$TARGET --git-dir=$GIT_DIR checkout -f

# Créer les dossiers nécessaires s'ils n'existent pas
mkdir -p $TARGET/data
mkdir -p $TARGET/public/uploads/oeuvres
mkdir -p $TARGET/public/uploads/expos
mkdir -p $TARGET/public/uploads/boutique

# Permissions
chmod 775 $TARGET/data
chmod 775 $TARGET/public/uploads
chmod 775 $TARGET/public/uploads/oeuvres
chmod 775 $TARGET/public/uploads/expos
chmod 775 $TARGET/public/uploads/boutique

echo ">> Déploiement terminé."
HOOK
chmod +x ~/deploy.git/hooks/post-receive'
```

### Ajouter le remote et pousser

```bash
cd /D1/web/users/denis/nathalie2
git remote add production nathalie:deploy.git
git push production master
```

## 5. Configuration Apache

Le DocumentRoot d'Apache doit pointer vers le sous-dossier `public/` du site.

### Option A : DocumentRoot direct

Dans la config Apache du vhost (sur terred) :

```apache
<VirtualHost *:443>
    ServerName thalyedoriam.fr
    ServerAlias www.thalyedoriam.fr
    DocumentRoot /home/nathalie/www/public

    <Directory /home/nathalie/www/public>
        AllowOverride All
        Require all granted
    </Directory>

    # SSL (Let's Encrypt ou autre)
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/thalyedoriam.fr/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/thalyedoriam.fr/privkey.pem
</VirtualHost>

# Redirection HTTP → HTTPS
<VirtualHost *:80>
    ServerName thalyedoriam.fr
    ServerAlias www.thalyedoriam.fr
    Redirect permanent / https://thalyedoriam.fr/
</VirtualHost>
```

### Option B : Si le DocumentRoot pointe déjà vers ~/www

Si Apache pointe vers `/home/nathalie/www/` (comme souvent sur les mutualisés), il faut que le contenu de `public/` soit à la racine de `www/`. Dans ce cas, adapter le hook post-receive :

```bash
# Dans le post-receive, déployer public/ à la racine de www/
git --work-tree=$HOME/site --git-dir=$GIT_DIR checkout -f
rsync -a --delete $HOME/site/public/ $TARGET/
# Et copier les dossiers app/templates/data à côté (hors DocumentRoot)
cp -r $HOME/site/app $HOME/app
cp -r $HOME/site/templates $HOME/templates
cp -r $HOME/site/data $HOME/data
```

Et modifier `public/index.php` pour ajuster les chemins :
```php
// Adapter APP_ROOT si les dossiers sont hors du DocumentRoot
define('APP_ROOT', dirname(__DIR__)); // ou un chemin absolu
```

**Note** : l'option A est recommandée car plus propre. Vérifier la config actuelle du WordPress pour savoir où pointe le DocumentRoot.

## 6. Configuration du site

### Variable d'environnement SITE_URL

Ajouter dans le `.htaccess` ou dans un `.env` sur le serveur :

```apache
# Dans .htaccess (avant les règles de rewrite)
SetEnv SITE_URL https://thalyedoriam.fr
```

Ou dans la config Apache du vhost :
```apache
SetEnv SITE_URL https://thalyedoriam.fr
```

### Désactiver le mode debug en production

Modifier `app/config.php` sur le serveur (ou via une variable d'env) :

```php
// Erreurs en prod : ne pas afficher
ini_set('display_errors', 0);
error_reporting(0);
```

**Recommandation** : ajouter une détection d'environnement dans `config.php` :

```php
$isProd = (getenv('APP_ENV') === 'production') || (strpos($_SERVER['HTTP_HOST'] ?? '', 'thalyedoriam.fr') !== false);

if ($isProd) {
    ini_set('display_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
```

## 7. Migrer les images

Les images des œuvres sont dans `public/uploads/oeuvres/`. Elles doivent être copiées sur le serveur :

```bash
# Via SSHFS
cp -r /D1/web/users/denis/nathalie2/public/uploads/* /D1/web/users/denis/nathalie/mnt/www/public/uploads/

# Ou via scp
scp -r /D1/web/users/denis/nathalie2/public/uploads/* nathalie:www/public/uploads/
```

**Important** : le dossier `public/uploads/` et `data/` doivent être dans le `.gitignore` pour ne pas être écrasés à chaque déploiement :

```gitignore
# Fichiers de données (ne pas écraser en prod)
data/site.db
public/uploads/oeuvres/*
public/uploads/expos/*
public/uploads/boutique/*
public/uploads/portrait.jpg
!public/uploads/.gitkeep
```

## 8. Base de données SQLite

La base `data/site.db` est créée automatiquement au premier accès si elle n'existe pas. Il suffit de :

1. S'assurer que le dossier `data/` existe et est en écriture (chmod 775)
2. Accéder au site → la base se crée avec le schéma
3. Aller dans `/admin` (mot de passe par défaut : `admin`)
4. **Changer le mot de passe immédiatement** via Réglages

Ou copier la base locale si elle contient déjà les bonnes données :

```bash
scp /D1/web/users/denis/nathalie2/data/site.db nathalie:www/data/site.db
```

## 9. Checklist de mise en production

- [ ] Sauvegarder le WordPress (fichiers + base MySQL)
- [ ] Configurer l'accès SSH au compte nathalie
- [ ] Initialiser le dépôt git et le remote production
- [ ] Déployer le code via `git push production master`
- [ ] Configurer Apache (DocumentRoot → `public/`)
- [ ] Ajouter `SetEnv SITE_URL https://thalyedoriam.fr`
- [ ] Désactiver `display_errors` en production
- [ ] Copier les images (uploads/) sur le serveur
- [ ] Copier ou recréer la base SQLite (data/site.db)
- [ ] Vérifier les permissions (data/ et uploads/ en écriture)
- [ ] Tester le site : accueil, galerie, contact, admin
- [ ] **Changer le mot de passe admin** (défaut : `admin`)
- [ ] Vérifier le SSL/HTTPS
- [ ] Tester sur mobile

## 10. Mises à jour futures

Pour déployer une mise à jour :

```bash
cd /D1/web/users/denis/nathalie2
git add -A
git commit -m "Description de la modification"
git push production master
```

Le hook post-receive met à jour automatiquement le site. Les fichiers `data/site.db` et `public/uploads/` ne sont pas touchés grâce au `.gitignore`.

## 11. Rollback (en cas de problème)

```bash
# Restaurer le WordPress depuis la sauvegarde
ssh nathalie "cd ~ && tar xzf backup-wordpress-*.tar.gz"

# Et remettre le DocumentRoot vers l'ancien dossier WordPress
```

---

## Structure sur le serveur (cible)

```
/home/nathalie/
├── deploy.git/          ← Bare repo git (réception des push)
├── backup-wordpress-*   ← Sauvegarde de l'ancien site
└── www/                 ← Site déployé
    ├── app/
    │   ├── config.php
    │   ├── database.php
    │   ├── router.php
    │   ├── auth.php
    │   └── functions.php
    ├── templates/
    │   ├── layout.php
    │   ├── pages/
    │   └── admin/
    ├── data/
    │   └── site.db        ← Base SQLite (créée auto)
    └── public/            ← DocumentRoot Apache
        ├── index.php
        ├── .htaccess
        ├── css/
        ├── js/
        ├── img/
        └── uploads/       ← Images uploadées
```
