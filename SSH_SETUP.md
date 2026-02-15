# Configuration SSH - Thalye d'Oriam

## 🔑 Paire de clés SSH créée

Vos clés SSH sont stockées dans le dossier `.ssh/` :
- **Clé privée** : `.ssh/id_ed25519_thalyedoriam` (à garder secrète !)
- **Clé publique** : `.ssh/id_ed25519_thalyedoriam.pub`

## 📋 Clé publique à ajouter au serveur

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIBS4x994BRTc1HIyTdjTJF44EbYzZpJ9XQmVy7dUX3WL nathalie@thalyedoriam
```

## 🚀 Installation de la clé sur le serveur

### Méthode automatique :
```bash
cat .ssh/id_ed25519_thalyedoriam.pub | ssh nathalie@terred.kdrezo.net "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"
```

### Méthode manuelle :
1. Connectez-vous au serveur : `ssh nathalie@terred.kdrezo.net`
2. Créez le dossier `.ssh` : `mkdir -p ~/.ssh`
3. Éditez le fichier : `nano ~/.ssh/authorized_keys`
4. Collez la clé publique ci-dessus
5. Sauvegardez et quittez

## 🔐 Utilisation de la clé

Une fois la clé installée sur le serveur, vous pourrez vous connecter sans mot de passe :

```bash
ssh -i .ssh/id_ed25519_thalyedoriam nathalie@terred.kdrezo.net
```

Ou configurez votre `~/.ssh/config` :

```
Host thalyedoriam
    HostName terred.kdrezo.net
    User nathalie
    IdentityFile ~/path/to/.ssh/id_ed25519_thalyedoriam
```

Puis connectez-vous simplement avec : `ssh thalyedoriam`

## ⚠️ Sécurité

- Ne partagez JAMAIS votre clé privée
- Les fichiers `.ssh/` et `CREDENTIALS.txt` sont déjà dans `.gitignore`
- Gardez ces fichiers en sécurité
