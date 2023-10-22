<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_')]
class AppController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
            'local_nav' => 'pupil'
        ]);
    }

    #[Route('/admin/setup', name: 'setup')]
    public function setup(): Response
    {
        return $this->render('app/setup.html.twig', [
            'local_nav' => 'settings'
        ]);
    }

}
