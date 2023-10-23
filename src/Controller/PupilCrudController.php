<?php

namespace App\Controller;

use App\Entity\Pupil;
use App\Form\PupilType;
use App\Repository\PupilRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pupil', name: 'app_pupil_crud_')]
class PupilCrudController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PupilRepository $pupilRepository): Response
    {
        return $this->render('pupil_crud/index.html.twig', [
            'pupils' => $pupilRepository->findAll(),
            'local_nav' => 'pupil'
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pupil = new Pupil();
        $form = $this->createForm(PupilType::class,$pupil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pupil);
            $entityManager->flush();

            return $this->redirectToRoute('app_pupil_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pupil_crud/new.html.twig', [
            'form' => $form,
            'local_nav' => 'pupil'
        ]);
    }
}
