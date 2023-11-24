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
            // TODO: Nicht für jeden Prüfling, sondern für jede Prüfung eine Zeile ausgeben!
            foreach ($pupil->getExams() as $exam){
                /** @var $exam Exam */
                $examPoints += $exam->getExamPoints();
                $exams[] = [$exam->getExamNumber(),$exam->getSubject(),$exam->getExamPoints()];
            }

            $birthdate = $pupil->getBirthDate() ? $pupil->getBirthDate()->format('d.m.Y') : '';
            $examDate = $pupil->getExamDate()  ? $pupil->getExamDate()->format('Y') : '';



            $data_array[$i] = [
                $pupil->getFirstname(),
                $pupil->getLastname(),
                $birthdate,
                $examDate,
                $pupil->getQualificationPoints(),
            ];
            foreach ($exams as $examItem) {
                foreach ($examItem as $examData) {
                    $data_array[$i][] = $examData;
                }
            }


            $i++;
        }
        $csv = new Csv();
        $csv->linefeed = "\n";

        // TODO: Jede Prüfung in eigener Zeile ausgeben, um PIVOT-Tables in Excel zu verbessern!
        $header = array('Vorname', 'Nachname','Geburtsdatum','Abiturjahr','Kursblock','Pr-A','Fach-A','Punkte-A','Pr-Nr.-B','Fach-B','Punkte-B','Pr-C.','Fach-C','Punkte-C','Pr-D.','Fach-D','Punkte-D','Pr-E.','Fach-E','Punkte-E');
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