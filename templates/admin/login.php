<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — <?= e(getSetting('site_title', "Thalye d'Oriam")) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-box">
            <h1><?= e(getSetting('site_title', "Thalye d'Oriam")) ?></h1>
            <p>Espace administration</p>
            <?php if (!empty($loginError)): ?>
                <p class="login-error">Mot de passe incorrect.</p>
            <?php endif; ?>
            <form method="POST" action="/admin/login">
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
