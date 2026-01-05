<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- 1. Catégorie: Tentes ---
        $categoryTentes = new Category();
        $categoryTentes->setName('Tentes');
        $categoryTentes->setEmoji('⛺');
        $categoryTentes->setDescription('Tentes de camping pour 2 à 8 personnes, imperméables et faciles à monter');
        $manager->persist($categoryTentes);

        $tentes = [
            ['Tente 2 Places Quechua', 'Tente ultra légère et compacte, idéale pour la randonnée. Montage rapide en 5 minutes.', 149.99],
            ['Tente Familiale 4 Places', 'Tente spacieuse avec 2 chambres séparées, hauteur 1,90m. Parfaite pour les familles.', 299.99],
            ['Tente Dôme 3 Places', 'Structure autoportante résistante au vent. Double toit imperméable 3000mm.', 189.99],
            ['Tente Tunnel 6 Places', 'Grande tente familiale avec salon et 2 chambres. Hauteur debout.', 449.99],
            ['Tente Bivouac Ultra Light', 'Seulement 1,2kg ! Parfaite pour le trek longue distance.', 199.99],
        ];

        foreach ($tentes as [$name, $desc, $price]) {
            $product = new Product();
            $product->setName($name)
                    ->setDescription($desc)
                    ->setPrice($price)
                    ->setStock(mt_rand(5, 20)) // Correction : ajout du stock
                    ->setCategory($categoryTentes);
            $manager->persist($product);
        }

        // --- 2. Catégorie: Sacs de couchage ---
        $categorySacs = new Category();
        $categorySacs->setName('Sacs de couchage');
        $categorySacs->setEmoji('🛏️');
        $categorySacs->setDescription('Sacs de couchage chauds et confortables pour toutes les saisons');
        $manager->persist($categorySacs);

        $sacs = [
            ['Sac de Couchage 10°C', 'Confortable jusqu\'à 10°C, garnissage synthétique. Poids: 1,5kg.', 59.99],
            ['Sac de Couchage 0°C', 'Pour les nuits fraîches. Isolation thermique renforcée.', 89.99],
            ['Duvet -10°C Extrême', 'Sac grand froid avec duvet naturel 90% oie. Ultra chaud.', 249.99],
            ['Sac Momie Ultralight', 'Sac compact 800g pour le trek. Température confort 15°C.', 129.99],
            ['Sac Double 2 Places', 'Sac de couchage double pour couple. Très spacieux.', 149.99],
        ];

        foreach ($sacs as [$name, $desc, $price]) {
            $product = new Product();
            $product->setName($name)
                    ->setDescription($desc)
                    ->setPrice($price)
                    ->setStock(mt_rand(5, 20)) // Correction : ajout du stock
                    ->setCategory($categorySacs);
            $manager->persist($product);
        }

        // --- 3. Catégorie: Sacs à dos ---
        $categorySacsDos = new Category();
        $categorySacsDos->setName('Sacs à dos');
        $categorySacsDos->setEmoji('🎒');
        $categorySacsDos->setDescription('Sacs de randonnée et trekking de 20L à 80L');
        $manager->persist($categorySacsDos);

        $sacsDos = [
            ['Sac à Dos 30L Randonnée', 'Sac de randonnée journée avec poche à eau. Dos ventilé.', 79.99],
            ['Sac Trekking 50L', 'Grand sac pour trek 5-7 jours. Système de portage réglable.', 159.99],
            ['Sac Expédition 70L', 'Sac technique pour expéditions longues. Ceinture ventrale rembourrée.', 249.99],
            ['Sac Ultra Compact 20L', 'Petit sac ultra léger pliable dans sa poche. Idéal sorties courtes.', 39.99],
            ['Sac Photo Nature 40L', 'Sac avec compartiment photo rembourré + espace camping.', 189.99],
        ];

        foreach ($sacsDos as [$name, $desc, $price]) {
            $product = new Product();
            $product->setName($name)
                    ->setDescription($desc)
                    ->setPrice($price)
                    ->setStock(mt_rand(5, 20)) // Correction : ajout du stock
                    ->setCategory($categorySacsDos);
            $manager->persist($product);
        }

        // --- 4. Catégorie: Réchauds et Cuisine ---
        $categoryRechauds = new Category();
        $categoryRechauds->setName('Réchauds & Cuisine');
        $categoryRechauds->setEmoji('🔥');
        $categoryRechauds->setDescription('Réchauds, popotes et ustensiles pour cuisiner en plein air');
        $manager->persist($categoryRechauds);

        $rechauds = [
            ['Réchaud Gaz 1 Feu', 'Réchaud compact à cartouche, allumage piezo. Puissance 2600W.', 49.99],
            ['Popote Camping 4 Pers', 'Set casseroles et poêle aluminium anodisé. Compact et léger.', 39.99],
            ['Réchaud Multi-combustible', 'Fonctionne essence, gaz, kérosène. Ultra polyvalent.', 149.99],
            ['Kit Couverts Camping', 'Fourchette, couteau, cuillère pliables en titane.', 24.99],
            ['Gourde Inox 1L', 'Gourde isotherme garde au chaud 12h, au froid 24h.', 34.99],
        ];

        foreach ($rechauds as [$name, $desc, $price]) {
            $product = new Product();
            $product->setName($name)
                    ->setDescription($desc)
                    ->setPrice($price)
                    ->setStock(mt_rand(5, 20)) // Correction : ajout du stock
                    ->setCategory($categoryRechauds);
            $manager->persist($product);
        }

        // --- 5. Catégorie: Éclairage ---
        $categoryEclairage = new Category();
        $categoryEclairage->setName('Éclairage');
        $categoryEclairage->setEmoji('🔦');
        $categoryEclairage->setDescription('Lampes frontales, lanternes et éclairage de camping');
        $manager->persist($categoryEclairage);

        $eclairages = [
            ['Lampe Frontale 300 Lumens', 'Rechargeable USB, 3 modes. Autonomie 10h.', 29.99],
            ['Lanterne LED Camping', 'Lanterne solaire + USB. Éclairage 360° réglable.', 44.99],
            ['Lampe Frontale Pro 1000 Lumens', 'Ultra puissante pour spéléo et trail nocturne.', 89.99],
            ['Guirlande LED Solaire', 'Guirlande 10m à panneaux solaires. Ambiance camp.', 24.99],
            ['Torche Tactique Étanche', 'Lampe torche robuste 500 lumens. IP68 étanche.', 39.99],
        ];

        foreach ($eclairages as [$name, $desc, $price]) {
            $product = new Product();
            $product->setName($name)
                    ->setDescription($desc)
                    ->setPrice($price)
                    ->setStock(mt_rand(5, 20)) // Correction : ajout du stock
                    ->setCategory($categoryEclairage);
            $manager->persist($product);
        }

        // --- 6. Catégorie: Navigation ---
        $categoryNavigation = new Category();
        $categoryNavigation->setName('Navigation');
        $categoryNavigation->setEmoji('🧭');
        $categoryNavigation->setDescription('Boussoles, GPS et cartes pour s\'orienter en randonnée');
        $manager->persist($categoryNavigation);

        $navigation = [
            ['Boussole Orienteering Pro', 'Boussole précision avec loupe et règle. Liquide anti-bulle.', 34.99],
            ['GPS Randonnée', 'GPS de randonnée avec cartes préchargées. Autonomie 20h.', 299.99],
            ['Porte-carte Étanche', 'Pochette transparente pour carte IGN. Tour de cou.', 14.99],
            ['Altimètre Barométrique', 'Montre altimètre, baromètre, boussole digitale.', 149.99],
            ['Kit Survie Orientation', 'Boussole + sifflet + miroir signal + allume-feu.', 19.99],
        ];

        foreach ($navigation as [$name, $desc, $price]) {
            $product = new Product();
            $product->setName($name)
                    ->setDescription($desc)
                    ->setPrice($price)
                    ->setStock(mt_rand(5, 20)) // Correction : ajout du stock
                    ->setCategory($categoryNavigation);
            $manager->persist($product);
        }

        // --- 7. Catégorie: Chaussures ---
        $categoryChaussures = new Category();
        $categoryChaussures->setName('Chaussures');
        $categoryChaussures->setEmoji('🥾');
        $categoryChaussures->setDescription('Chaussures et bottes de randonnée imperméables');
        $manager->persist($categoryChaussures);

        $chaussures = [
            ['Chaussures Randonnée Basses', 'Tige basse respirante, semelle Vibram. Poids: 350g.', 89.99],
            ['Bottes Trekking Montantes', 'Bottes cuir imperméables Gore-Tex. Support cheville.', 169.99],
            ['Sandales Randonnée', 'Sandales sport fermées pour rivière et sentier.', 59.99],
            ['Chaussures Trail Running', 'Légères et accrochantes pour terrain technique.', 129.99],
            ['Bottes Hiver -20°C', 'Bottes grand froid isolées, cramponnables.', 249.99],
        ];

        foreach ($chaussures as [$name, $desc, $price]) {
            $product = new Product();
            $product->setName($name)
                    ->setDescription($desc)
                    ->setPrice($price)
                    ->setStock(mt_rand(5, 20)) // Correction : ajout du stock
                    ->setCategory($categoryChaussures);
            $manager->persist($product);
        }

        // --- 8. Catégorie: Accessoires ---
        $categoryAccessoires = new Category();
        $categoryAccessoires->setName('Accessoires');
        $categoryAccessoires->setEmoji('🔧');
        $categoryAccessoires->setDescription('Couteaux, outils multifonctions et accessoires pratiques');
        $manager->persist($categoryAccessoires);

        $accessoires = [
            ['Couteau Suisse Multifonction', 'Couteau 12 outils: lame, scie, tournevis, ouvre-bouteille...', 44.99],
            ['Sifflet de Survie', 'Sifflet très puissant 120dB. Avec boussole intégrée.', 9.99],
            ['Kit Premier Secours', 'Trousse complète 100 pièces pour randonnée et camping.', 29.99],
            ['Corde Paracorde 30m', 'Corde robuste 550 paracord. Charge rupture 250kg.', 19.99],
            ['Hamac Camping Double', 'Hamac ultra-résistant 200kg avec moustiquaire intégrée.', 79.99],
        ];

        foreach ($accessoires as [$name, $desc, $price]) {
            $product = new Product();
            $product->setName($name)
                    ->setDescription($desc)
                    ->setPrice($price)
                    ->setStock(mt_rand(5, 20)) // Correction : ajout du stock
                    ->setCategory($categoryAccessoires);
            $manager->persist($product);
        }

        $manager->flush();
    }
}