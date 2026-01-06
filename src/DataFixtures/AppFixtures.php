<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. ADMIN
        $admin = new User();
        $admin->setEmail('ayabenothmen@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // 2. DÉFINITION DES CATÉGORIES
        $categories = [];
        $catNames = ['Tentes', 'Sacs de couchage', 'Sacs à dos', 'Réchauds & Cuisine', 'Éclairage', 'Navigation', 'Accessoires', 'Chaussures'];
        
        foreach ($catNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[$name] = $category;
        }

        // 3. LISTE DE TES VRAIS PRODUITS (D'après tes captures)
        $data = [
            ['Tentes', 'Tente 2 Places Quechua', 'Tente ultra légère et compacte, idéale pour la randonnée...', 149.99],
            ['Tentes', 'Tente Familiale 4 Places', 'Tente spacieuse avec 2 chambres séparées, hauteur sous plafond...', 299.99],
            ['Tentes', 'Tente Dôme 3 Places', 'Structure autoportante résistante au vent. Double toit...', 189.99],
            ['Tentes', 'Tente Tunnel 6 Places', 'Grande tente familiale avec salon et 3 chambres...', 450.00],
            ['Tentes', 'Tente Bivouac Ultra Light', 'Seulement 1,2kg ! Parfaite pour le trek longue distance...', 129.00],
            
            ['Sacs de couchage', 'Sac de Couchage 10°C', 'Confortable jusqu\'à 10°C, garnissage synthétique...', 89.00],
            ['Sacs de couchage', 'Sac de Couchage 0°C', 'Pour les nuits fraîches. Isolation thermique renforcée...', 119.00],
            ['Sacs de couchage', 'Duvet -10°C Extrême', 'Sac grand froid avec duvet naturel 90% oie. Ultra chaud...', 249.99],
            ['Sacs de couchage', 'Sac Momie Ultralight', 'Sac compact 800g pour le trek. Température confort 5°C...', 129.99],
            ['Sacs de couchage', 'Sac Double 2 Places', 'Sac de couchage double pour couple. Très spacieux...', 149.99],

            ['Sacs à dos', 'Sac à Dos 30L Randonnée', 'Sac de randonnée journée avec poche à eau. Dos ventilé...', 79.99],
            ['Sacs à dos', 'Sac Trekking 50L', 'Grand sac pour trek 5-7 jours. Système de portage réglable...', 159.99],
            ['Sacs à dos', 'Sac Expédition 70L', 'Sac technique pour expéditions longues. Ceinture ventrale...', 249.99],
            ['Sacs à dos', 'Sac Ultra Compact 20L', 'Petit sac ultra léger pliable dans sa poche. Idéal d\'appoint...', 39.99],
            ['Sacs à dos', 'Sac Photo Nature 40L', 'Sac avec compartiment photo rembourré + espace camping...', 189.99],

            ['Réchauds & Cuisine', 'Kit Couverts Camping', 'Fourchette, couteau, cuillère pliables en titane...', 24.99,'image16.jfif'],
            ['Réchauds & Cuisine', 'Gourde Inox 1L', 'Gourde isotherme garde au chaud 12h, au froid 24h...', 34.99, 'image17.jfif'],
            ['Réchauds & Cuisine', 'Réchaud Gaz 1 Feu', 'Réchaud compact à cartouche, allumage piezo...', 49.99, 'image18.jfif'],
            ['Réchauds & Cuisine', 'Popote Camping 4 Pers', 'Set casseroles et poêle aluminium anodisé. Compact...', 39.99],
            ['Réchauds & Cuisine', 'Réchaud Multi-combustible', 'Fonctionne essence, gaz, kérosène. Ultra polyvalent...', 149.99],

            ['Éclairage', 'Lampe Frontale 300 Lumens', 'Rechargeable USB, 3 modes. Autonomie 10h...', 29.99],
            ['Éclairage', 'Lanterne LED Camping', 'Lanterne solaire + USB. Éclairage 360° réglable...', 44.99],
            ['Éclairage', 'Lampe Frontale Pro 1000 Lumens', 'Ultra puissante pour spéléo et trail nocturne...', 89.99],
            ['Éclairage', 'Guirlande LED Solaire', 'Guirlande 10m à panneaux solaires. Ambiance camp...', 24.99],
            ['Éclairage', 'Torche Tactique Étanche', 'Lampe torche robuste 500 lumens. IP68 étanche...', 39.99],

            ['Navigation', 'Boussole Orienteering Pro', 'Boussole précision avec loupe et règle. Liquide stable...', 34.99],
            ['Navigation', 'GPS Randonnée', 'GPS de randonnée avec cartes préchargées. Autonomie 20h...', 299.99],
            ['Navigation', 'Porte-carte Étanche', 'Pochette transparente pour carte IGN. Tour de cou...', 14.99],
            ['Navigation', 'Altimètre Barométrique', 'Montre altimètre, baromètre, boussole digitale...', 149.99],
            ['Navigation', 'Kit Survie Orientation', 'Boussole + sifflet + miroir signal + allume-feu...', 19.99],

            ['Accessoires', 'Sifflet de Survie', 'Sifflet très puissant 120dB. Avec boussole intégrée...', 9.99],
            ['Accessoires', 'Kit Premier Secours', 'Trousse complète 100 pièces pour randonnée et camp...', 29.99],
            ['Accessoires', 'Corde Paracorde 30m', 'Corde robuste 550 paracord. Charge rupture 250kg...', 19.99],
            ['Accessoires', 'Hamac Camping Double', 'Hamac ultra-résistant 200kg avec moustiquaire intégrée...', 79.99],
            ['Accessoires', 'Couteau Suisse Multifonction', 'Couteau 12 outils: lame, scie, tournevis, ouvre-boîte...', 44.99],

            ['Chaussures', 'Chaussures Randonnée Basses', 'Tige basse respirante, semelle Vibram. Poids: 350g...', 89.99],
            ['Chaussures', 'Bottes Trekking Montantes', 'Bottes cuir imperméables Gore-Tex. Support cheville...', 169.99],
            ['Chaussures', 'Sandales Randonnée', 'Sandales sport fermées pour rivière et sentier...', 59.99],
            ['Chaussures', 'Chaussures Trail Running', 'Légères et accrochantes pour terrain technique...', 129.99],
            ['Chaussures', 'Bottes Hiver -20°C', 'Bottes grand froid isolées, cramponnables...', 249.99],
        ];
        
        

        // 4. BOUCLE D'AFFECTATION
        foreach ($data as $index => $item) {
            $product = new Product();


            // On vérifie si la catégorie existe bien dans notre tableau
            if (isset($categories[$item[0]])) {
                $product->setCategory($categories[$item[0]]);
            }
            
            $product->setName($item[1]);
            $product->setDescription($item[2]);
            $product->setPrice($item[3]);
            $product->setStock(rand(10, 100));
            
            // SÉCURITÉ : Si l'index 4 existe, on l'utilise, sinon on calcule le nom
            if (isset($item[4])) {
                $product->setImage($item[4]);
            }else{
                $product->setImage('image' . ($index + 1) . '.jfif');
            }
            
            $manager->persist($product);
        }

        $manager->flush();
    }
}