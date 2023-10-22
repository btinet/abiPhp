<?php

namespace App\Controller;

use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pupil', name: 'app_pupil_crud_')]
class PupilCrudController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('pupil_crud/index.html.twig', [
            'local_nav' => 'pupil'
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(TeacherRepository $teacherRepository): Response
    {
        $teachers = $teacherRepository->findAll();

        return $this->render('pupil_crud/new.html.twig', [
            'teachers' => $teachers,
            'local_nav' => 'pupil'
        ]);
    }
}
