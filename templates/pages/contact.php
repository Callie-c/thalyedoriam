<?php
$flash = getFlash();
$sujet = $_GET['sujet'] ?? '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $flash = ['type' => 'error', 'message' => 'Erreur de sécurité, veuillez réessayer.'];
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sujetPost = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$nom || !$email || !$message) {
            $flash = ['type' => 'error', 'message' => 'Veuillez remplir tous les champs obligatoires.'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $flash = ['type' => 'error', 'message' => 'Adresse email invalide.'];
        } else {
            $contactEmail = $settings['contact_email'] ?? '';
            if ($contactEmail) {
                $headers = "From: $nom <$email>\r\n";
                $headers .= "Reply-To: $email\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                $body = "Nom : $nom\n";
                $body .= "Email : $email\n";
                $body .= "Sujet : $sujetPost\n\n";
                $body .= "Message :\n$message\n";

                $subject = "Contact site — " . ($sujetPost ?: 'Nouveau message');
                mail($contactEmail, $subject, $body, $headers);
            }
            $flash = ['type' => 'success', 'message' => 'Votre message a été envoyé. Merci !'];
            $nom = $email = $sujetPost = $message = '';
        }
    }
}
?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="section-inner">
        <h1 class="section-title">Contact</h1>
        <div class="section-divider"></div>

        <?php if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <div class="contact-layout">
            <div>
                <form method="POST" action="/contact">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" value="<?= e($nom ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?= e($email ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="sujet">Sujet</label>
                        <input type="text" id="sujet" name="sujet" value="<?= e($sujet ?: ($sujetPost ?? '')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required><?= e($message ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            </div>
            <div class="contact-info">
                <h3>Coordonnées</h3>
                <?php if (!empty($settings['contact_email'])): ?>
                    <p>✉ <a href="mailto:<?= e($settings['contact_email']) ?>"><?= e($settings['contact_email']) ?></a></p>
                <?php endif; ?>
                <?php if (!empty($settings['contact_phone'])): ?>
                    <p>☎ <?= e($settings['contact_phone']) ?></p>
                <?php endif; ?>
                <?php if (!empty($settings['contact_address'])): ?>
                    <p style="margin-top: 1rem;"><?= nl2br(e($settings['contact_address'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($settings['instagram']) || !empty($settings['facebook'])): ?>
                    <h3 style="margin-top: 2rem;">Suivez-moi</h3>
                    <?php if (!empty($settings['instagram'])): ?>
                        <p><a href="<?= e($settings['instagram']) ?>" target="_blank" rel="noopener">Instagram</a></p>
                    <?php endif; ?>
                    <?php if (!empty($settings['facebook'])): ?>
                        <p><a href="<?= e($settings['facebook']) ?>" target="_blank" rel="noopener">Facebook</a></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
