<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="section-inner legal-content">
        <h1 class="section-title">Mentions légales</h1>
        <div class="section-divider"></div>

        <h2>Éditeur du site</h2>
        <p>
            <?= e($settings['site_title'] ?? "Thalye d'Oriam") ?><br>
            <?php if (!empty($settings['contact_address'])): ?>
                <?= nl2br(e($settings['contact_address'])) ?><br>
            <?php endif; ?>
            <?php if (!empty($settings['contact_email'])): ?>
                Email : <a href="mailto:<?= e($settings['contact_email']) ?>"><?= e($settings['contact_email']) ?></a>
            <?php endif; ?>
        </p>

        <h2>Propriété intellectuelle</h2>
        <p>L'ensemble du contenu de ce site (textes, images, œuvres, photographies) est protégé par le droit d'auteur. Toute reproduction, même partielle, est interdite sans autorisation préalable de l'artiste.</p>

        <h2>Données personnelles</h2>
        <p>Les informations recueillies via le formulaire de contact sont uniquement destinées à répondre à vos demandes. Elles ne sont ni cédées ni vendues à des tiers. Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression de vos données en contactant l'éditeur du site.</p>

        <h2>Cookies</h2>
        <p>Ce site utilise uniquement des cookies techniques nécessaires à son fonctionnement (session). Aucun cookie de suivi ou publicitaire n'est utilisé.</p>

        <h2>Hébergement</h2>
        <p>Ce site est hébergé par : [À compléter]</p>
    </div>
</section>
