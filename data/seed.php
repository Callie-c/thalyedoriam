<?php
/**
 * Script de remplissage initial du contenu du site.
 * Usage : php data/seed.php
 */

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/database.php';
require_once __DIR__ . '/../app/functions.php';

$db = getDb();

// ---- Biographie ----
$bio = <<<'BIO'
Née en 1963, Thalye d'Oriam est une artiste peintre contemporaine française.

Son travail se distingue par une technique singulière mêlant peinture, verre, résine et éléments décoratifs sur bois. Après avoir renforcé la plaque de bois d'okoumé afin qu'elle ne bouge pas avec le temps, elle prépare la surface avec une sous-couche puis peint le fond en utilisant divers supports pour créer profondeur et relief. Elle organise ensuite des coupes de verre préalablement peintes et décorées, avant d'appliquer de la résine pour obtenir brillance, transparence et reflets avec un effet miroir.

Sa démarche artistique repose sur la recherche de l'aléatoire des effets. Elle imagine souvent des histoires simples reliant les éléments primordiaux — le feu, la terre, l'eau, l'éther — à travers la singularité des formes qu'ils offrent.

À travers ses créations, elle aspire à faire émerger la beauté, l'élégance, la sérénité, la contemplation, la féminité, la force, le changement, la roue de la vie, le ciel, la terre, le sacré et la parure.

Elle est représentée par la Concept Store Gallery (Club des Ateliers d'Artistes) à La Baule et Paris, et ses œuvres sont disponibles sur Artsper.
BIO;

setSetting('bio_text', $bio);
echo "✓ Biographie enregistrée\n";

// ---- Texte d'accueil ----
$accueil = <<<'ACCUEIL'
Artiste peintre contemporaine, Thalye d'Oriam crée des œuvres uniques mêlant peinture, verre et résine sur bois d'okoumé. Chaque pièce joue avec la lumière, la transparence et les reflets pour révéler la beauté des éléments primordiaux.
ACCUEIL;

setSetting('accueil_text', $accueil);
echo "✓ Texte d'accueil enregistré\n";

// ---- Œuvres ----
$oeuvres = [
    [
        'titre' => "La fleur de verre et ses joyaux, Elegance",
        'description' => "Série fleur stylisée. Une composition délicate mêlant verre, résine et bijoux, où la fleur devient un joyau lumineux dans un univers de transparence et de reflets.",
        'technique' => "Technique mixte — résine, verre, bijoux sur bois",
        'dimensions' => "65 × 56 × 0,8 cm",
        'annee' => 2024,
        'ordre' => 1,
    ],
    [
        'titre' => "La fleur de verre et ses petites bêtes",
        'description' => "Série fleur stylisée. Libellules et papillons viennent se poser autour d'une fleur de verre, créant un petit monde poétique où nature et matière se rencontrent.",
        'technique' => "Technique mixte — résine, verre sur bois",
        'dimensions' => "65 × 56 × 0,8 cm",
        'annee' => 2024,
        'ordre' => 2,
    ],
    [
        'titre' => "Une histoire de fleur et de papillons",
        'description' => "Série fleur stylisée. Un dialogue entre la fleur et les papillons, entre la fixité du verre et la légèreté du vol, dans une harmonie de tons beiges et bleu ciel.",
        'technique' => "Technique mixte — résine, verre sur bois",
        'dimensions' => "65 × 56 × 0,8 cm",
        'annee' => 2024,
        'ordre' => 3,
    ],
    [
        'titre' => "Quand la douceur enfante la terre et ses merveilles",
        'description' => "Portrait féminin poétique. Une figure féminine d'où naissent les merveilles de la terre, dans une vision où douceur et puissance créatrice se mêlent.",
        'technique' => "Technique mixte — résine, verre sur bois",
        'dimensions' => "80 × 100 × 2 cm",
        'annee' => 2024,
        'ordre' => 4,
    ],
    [
        'titre' => "La femme au chapeau, Force et douceur",
        'description' => "Portrait féminin poétique. Force et douceur cohabitent dans ce portrait où la femme au chapeau incarne l'élégance et la sérénité.",
        'technique' => "Technique mixte — résine, verre sur bois",
        'dimensions' => "100 × 80 × 2 cm",
        'annee' => 2024,
        'ordre' => 5,
    ],
    [
        'titre' => "La danse éphémère des ancres",
        'description' => "Portrait féminin poétique. Entre ancrage et mouvement, cette œuvre explore la dualité entre ce qui nous retient et ce qui nous libère.",
        'technique' => "Technique mixte — résine, verre sur bois",
        'dimensions' => "100 × 80 × 2 cm",
        'annee' => 2024,
        'ordre' => 6,
    ],
    [
        'titre' => "La femme aux ailes de lumière",
        'description' => "Portrait féminin poétique. La lumière devient ailes, la résine capture les reflets, et la femme s'élève dans un halo de transparence dorée.",
        'technique' => "Technique mixte — résine, verre sur bois",
        'dimensions' => "100 × 80 × 2 cm",
        'annee' => 2024,
        'ordre' => 7,
    ],
    [
        'titre' => "La femme sculpture et la guirlande de fleurs",
        'description' => "Portrait féminin poétique. La femme devient sculpture, parée d'une guirlande de fleurs qui l'entoure comme un hommage à la féminité et au sacré.",
        'technique' => "Technique mixte — résine, verre sur bois",
        'dimensions' => "100 × 80 × 2 cm",
        'annee' => 2024,
        'ordre' => 8,
    ],
];

$stmt = $db->prepare('INSERT INTO oeuvres (titre, description, technique, dimensions, annee, image, ordre, visible, created_at) VALUES (:titre, :description, :technique, :dimensions, :annee, "", :ordre, 1, :created_at)');

foreach ($oeuvres as $o) {
    $stmt->execute([
        'titre' => $o['titre'],
        'description' => $o['description'],
        'technique' => $o['technique'],
        'dimensions' => $o['dimensions'],
        'annee' => $o['annee'],
        'ordre' => $o['ordre'],
        'created_at' => date('Y-m-d H:i:s'),
    ]);
    echo "✓ Œuvre ajoutée : {$o['titre']}\n";
}

// ---- Infos galerie / contact ----
setSetting('contact_address', "Club des Ateliers d'Artistes\n140 Avenue du Général de Gaulle\n44500 La Baule");
echo "✓ Adresse enregistrée\n";

echo "\n✅ Contenu initial chargé avec succès !\n";
echo "   — Biographie renseignée\n";
echo "   — Texte d'accueil renseigné\n";
echo "   — " . count($oeuvres) . " œuvres ajoutées\n";
echo "   — Adresse de la galerie renseignée\n";
echo "\n💡 Les images des œuvres doivent être uploadées via l'admin (/admin/oeuvres).\n";
