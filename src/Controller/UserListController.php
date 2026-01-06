<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class UserListController extends AbstractController
{
    #[Route('/userlist', name: 'app_user_list')]
    public function index( Request $request, UserRepository $userRepository): Response
    {
        $users =$userRepository->findAll();

        $search = $request->query->get('search');


        if ($search) {
            $users = $userRepository->findBySearch($search);
        } else {

            $users = $userRepository->findAll();
        }


        return $this->render('admin/userlist.html.twig', [
            'users' => $users,
        ]);

    }
    #[Route('/delete/{id}', name: 'delete_user', methods: ['POST'])]
    public function delete_us (EntityManagerInterface $em , UserRepository $er , $id){
        $users =$er->find($id);
        $em->remove($users);
        $em->flush();
        return $this->redirectToRoute('app_user_list');
    }




}
