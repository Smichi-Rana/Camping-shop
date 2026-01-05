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
        // 1. CRÉATION DE L'ADMIN
        $admin = new User();
        $admin->setEmail('ayabenothmen@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // 2. CRÉATION D'UNE CATÉGORIE
        $category = new Category();
        $category->setName('Matériel de base');
        $manager->persist($category);

        // 3. CRÉATION DE PRODUITS (Avec le champ STOCK !)
        for ($i = 1; $i <= 12; $i++) {
            $product = new Product();
            $product->setName('Produit Camping ' . $i);
            $product->setDescription('Description détaillée du matériel n°' . $i);
            $product->setPrice(rand(15, 250));
            $product->setCategory($category);
            
            // ON AJOUTE LE STOCK ICI POUR ÉVITER L'ERREUR
            $product->setStock(rand(5, 50)); 
            
            // Si ton entité a un champ image, on lui met une valeur par défaut
            // $product->setImage('default.jpg'); 
            
            $manager->persist($product);
        }

        $manager->flush();
    }
}