<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/teacher/crud')]
class TeacherCrudController extends AbstractController
{
    #[Route('/', name: 'app_teacher_crud_index', methods: ['GET'])]
    public function index(TeacherRepository $teacherRepository): Response
    {
        return $this->render('teacher_crud/index.html.twig', [
            'teachers' => $teacherRepository->findAll(),
            'local_nav' => 'settings'
        ]);
    }

    #[Route('/new', name: 'app_teacher_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('app_teacher_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('teacher_crud/new.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
            'local_nav' => 'settings'
        ]);
    }

    #[Route('/{id}', name: 'app_teacher_crud_show', methods: ['GET'])]
    public function show(Teacher $teacher): Response
    {
        return $this->render('teacher_crud/show.html.twig', [
            'teacher' => $teacher,
            'local_nav' => 'settings'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_teacher_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_teacher_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('teacher_crud/edit.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
            'local_nav' => 'settings'
        ]);
    }

    #[Route('/{id}', name: 'app_teacher_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$teacher->getId(), $request->request->get('_token'))) {
            $entityManager->remove($teacher);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_teacher_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
