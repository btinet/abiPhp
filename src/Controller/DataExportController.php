<?php

namespace App\Controller;


use App\Entity\Exam;
use App\Repository\PupilRepository;
use ParseCsv\Csv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/export', name: 'app_export_')]
class DataExportController extends AbstractController
{

    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        return $this->render('data/export.html.twig', [
            'local_nav' => 'export',
        ]);
    }

    #[Route('/pupil/all', name: 'pupil_all')]
    public function exportPupilAll(PupilRepository $pupilRepository): Response
    {

        $data_array = [];
        $i = 0;
        foreach ($pupilRepository->findAll() as $pupil)
        {
            $exams = [];
            $examPoints = 0;

            $birthdate = $pupil->getBirthDate() ? $pupil->getBirthDate()->format('d.m.Y') : '';
            $examDate = $pupil->getExamDate()  ? $pupil->getExamDate()->format('Y') : '';

            foreach ($pupil->getExams() as $exam){
                /** @var $exam Exam */
                $examPoints += $exam->getExamPoints();
                $exams[] = [];

                $data_array[$i] = [
                    $pupil->getFirstname(),
                    $pupil->getLastname(),
                    $birthdate,
                    $examDate,
                    $pupil->getQualificationPoints(),
                    $exam->getExamNumber(),
                    $exam->getSubject(),
                    $exam->getExamPoints()
                ];
                $i++;
            }


        }
        $csv = new Csv();
        $csv->linefeed = "\n";

        $header = array('Vorname', 'Nachname','Geburtsdatum','Abiturjahr','Kursblock','Pruefung','Fach','Punkte');
        $csv->output('Prüflinge.csv', $data_array, $header, ';');
        $response = new StreamedResponse(function() use ($csv) {
        });
        $response->headers->set('Content-Type', 'text/csv');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $csv->output_filename
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

}