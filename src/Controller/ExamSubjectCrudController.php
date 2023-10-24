<?php

namespace App\Controller;

use App\Entity\ExamSubject;
use App\Form\ExamSubjectType;
use App\Repository\ExamSubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/setup/exam_subject', name: 'app_exam_subject_crud_')]
class ExamSubjectCrudController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ExamSubjectRepository $examSubjectRepository): Response
    {
        return $this->render('exam_subject_crud/index.html.twig', [
            'exam_subjects' => $examSubjectRepository->findAll(),
            'local_nav' => 'settings',
            'side_nav' => 'exam_subject',
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $examSubject = new ExamSubject();
        $form = $this->createForm(ExamSubjectType::class, $examSubject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($examSubject);
            $entityManager->flush();

            return $this->redirectToRoute('app_exam_subject_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exam_subject_crud/new.html.twig', [
            'exam_subject' => $examSubject,
            'form' => $form,
            'local_nav' => 'settings',
            'side_nav' => 'exam_subject',
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(ExamSubject $examSubject): Response
    {
        return $this->render('exam_subject_crud/show.html.twig', [
            'exam_subject' => $examSubject,
            'local_nav' => 'settings',
            'side_nav' => 'exam_subject',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExamSubject $examSubject, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExamSubjectType::class, $examSubject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_exam_subject_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exam_subject_crud/edit.html.twig', [
            'exam_subject' => $examSubject,
            'form' => $form,
            'local_nav' => 'settings',
            'side_nav' => 'exam_subject',
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, ExamSubject $examSubject, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examSubject->getId(), $request->request->get('_token'))) {
            $entityManager->remove($examSubject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_exam_subject_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
