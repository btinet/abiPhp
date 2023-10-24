<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAndPasswordType;
use App\Form\UserPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/setup/user', name: 'app_user_crud_')]
class UserCrudController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user_crud/index.html.twig', [
            'users' => $userRepository->findAll(),
            'local_nav' => 'settings',
            'side_nav' => 'user',
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserAndPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_crud/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'local_nav' => 'settings',
            'side_nav' => 'user',
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user_crud/show.html.twig', [
            'user' => $user,
            'local_nav' => 'settings',
            'side_nav' => 'user',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_crud/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'local_nav' => 'settings',
            'side_nav' => 'user',
        ]);
    }

    #[Route('/{id}/edit-password', name: 'edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_crud/edit_password.html.twig', [
            'user' => $user,
            'form' => $form,
            'local_nav' => 'settings',
            'side_nav' => 'user',
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
