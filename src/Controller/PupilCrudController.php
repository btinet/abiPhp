<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Entity\ExtendedExam;
use App\Entity\Pupil;
use App\Form\ExamEditType;
use App\Form\ExamType;
use App\Form\PupilType;
use App\Repository\ExamRepository;
use App\Repository\GradeRepository;
use App\Repository\PupilRepository;
use App\Service\SortService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Environment;

#[Route('/admin/pupil', name: 'app_pupil_crud_')]
class PupilCrudController extends AbstractController
{

    private $twig;
    private $pdf;
    private $entrypointLookup;

    #[Route('/', name: 'index')]
    public function index(PupilRepository $pupilRepository): Response
    {
        return $this->render('pupil_crud/index.html.twig', [
            'pupils' => $pupilRepository->findAll(),
            'local_nav' => 'pupil'
        ]);
    }

    #[Route('/{id}/export/pdf', name: 'export_pdf')]
    public function pdfAction( Pupil $pupil, Environment $twig, EntrypointLookupInterface $entrypointLookup, ExamRepository $examRepository, GradeRepository $gradeRepository)
    {
        // Versuch ohne WKHTMLTOPDF
        $pdfOptions = new Options();
        $pdfOptions->setIsPhpEnabled(true);
        $pdfOptions->setIsJavascriptEnabled(true);
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setDefaultFont("Arial");
        $pdf2 = new Dompdf($pdfOptions);

        if($examPointsArray = $examRepository->sumExamPoints($pupil)) {
            $examPoints = array_pop($examPointsArray[0]);
        } else {
            $examPoints = 0;
        }

        $grades = $gradeRepository->findAll();

        $qualificationPoints = $pupil->getQualificationPoints();
        $examPointsSum = $examPoints*4;
        $points = $qualificationPoints + $examPointsSum;
        $pupilGrade = 0;
        $nextGrade = 0;
        $nextPoints = 0;
        $prevGrade = 0;
        $prevPoints = 0;

        foreach ($grades as $grade){
            if($points >= $grade->getMin() and $points <= $grade->getMax()) {
                $pupilGrade = $grade->getGrade();
            } elseif ($points > $grade->getMax()) {
                $prevGrade = $grade->getGrade();
                $prevPoints = $grade->getMax();
            } elseif ($points < $grade->getMin()) {
                $nextGrade = $grade->getGrade();
                $nextPoints = $grade->getMin();
                break;
            }
        }

        $diff = $examPointsSum + $nextPoints - $points;

        $extendedExams = [];

        foreach ($pupil->getExams() as $exam) {

            $x1 = ($exam->getExamPoints()*4 + + $prevPoints - $points) *3/4 - ($exam->getExamPoints()*2);
            $x2 = ($exam->getExamPoints()*4 + + $nextPoints - $points) *3/4 - ($exam->getExamPoints()*2);
            $x = round($x2);
            uasort($grades,new SortService('grade'));

            if ($exam->getExamNumber() <= 3) {
                $y = array_shift($grades);
                if($x <= 15 and $points < $y->getMin()) {
                    $eExam = new ExtendedExam();
                    $eExam->setExamNumber($exam->getExamNumber());
                    $eExam->setNeededExamPoints($x);
                    $eExam->setNeededExamPoints2($exam->getExamPoints());
                    $eExam->setSubject($exam->getSubject());
                    $eExam->setCriticalPoints(round($x1));
                    $extendedExams[] = $eExam;

                }
            }
        }

        $this->twig = $twig;
        //$this->pdf = $pdf;
        //$this->pdf->setOption('enable-local-file-access', true);
        $this->entrypointLookup = $entrypointLookup;
        $this->entrypointLookup->reset();
        $html = $this->twig->render('pdf_base.html.twig', [
            'pupil' => $pupil,
            'examPoints' => $examPoints,
            'grade' => $pupilGrade,
            'higherGrade' => $nextGrade,
            'extended_exams' => $extendedExams,
            'diff' => $diff + $qualificationPoints,
        ]);
        //$pdf = $this->pdf->getOutputFromHtml($html);
        $pdf2->setPaper('A4');
        $pdf2->loadHtml($html);
        $pdf2->render();

        $filename = sprintf('%s-%s.pdf',$pupil, date('Y-m-d'));



         return new Response(
            $pdf2->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );


    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pupil = new Pupil();
        $form = $this->createForm(PupilType::class,$pupil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pupil);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Der Prüfling '.$pupil.' wurde erfolgreich angelegt.'
            );

            return $this->redirectToRoute('app_pupil_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pupil_crud/new.html.twig', [
            'pupil' => $pupil,
            'form' => $form,
            'local_nav' => 'pupil'
        ]);
    }

    #[Route('/{id}/exam/edit', name: 'exam_edit', methods: ['GET', 'POST'])]
    public function examEdit(Request $request, EntityManagerInterface $entityManager, Exam $exam): Response
    {
        $form = $this->createForm(ExamEditType::class,$exam);
        $examNumber = $exam->getExamNumber();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exam->setExamNumber($examNumber);
            $entityManager->persist($exam);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Die '.$exam->getExamNumber().'. Prüfung wurde erfolgreich aktualisiert.'
            );

            return $this->redirectToRoute('app_pupil_crud_show', ['id' => $exam->getPupil()->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('pupil_crud/edit_exam.html.twig', [
            'pupil' => $exam->getPupil(),
            'exam' => $exam,
            'form' => $form,
            'local_nav' => 'pupil',
            'side_nav' => '',
        ]);
    }

    #[Route('/{id}/exam/add', name: 'exam_add', methods: ['GET', 'POST'])]
    public function examAdd(Request $request, EntityManagerInterface $entityManager, Pupil $pupil): Response
    {
        if($pupil->getExams()->count() == 5) {

            $this->addFlash(
                'error',
                'Es existieren bereits 5 Prüfungen.'
            );

            return $this->redirectToRoute('app_pupil_crud_show', ['id' => $pupil->getId()], Response::HTTP_SEE_OTHER);
        }
        $exam = new Exam();
        $exam->setPupil($pupil);

        $form = $this->createForm(ExamType::class,$exam, [
            'action' => $this->generateUrl('app_pupil_crud_exam_add',['id' => $pupil->getId()]),
            'method' => 'POST',
            'custom_option'=>$pupil
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exam);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Die Prüfung wurde erfolgreich angelegt.'
            );

            return $this->redirectToRoute('app_pupil_crud_show', ['id' => $pupil->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pupil_crud/add_exam.html.twig', [
            'pupil' => $pupil,
            'exam' => $exam,
            'form' => $form,
            'local_nav' => 'pupil',
            'side_nav' => '',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Pupil $pupil, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PupilType::class,$pupil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pupil);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Datensatz von ' . $pupil . ' wurde erfolgreich aktualisiert.'
            );

            return $this->redirectToRoute('app_pupil_crud_show', ['id' => $pupil->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pupil_crud/edit.html.twig', [
            'pupil' => $pupil,
            'form' => $form,
            'local_nav' => 'pupil',
            'side_nav' => 'overview',
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Pupil $pupil, ExamRepository $examRepository, GradeRepository $gradeRepository): Response
    {
        if($examPointsArray = $examRepository->sumExamPoints($pupil)) {
            $examPoints = array_pop($examPointsArray[0]);
        } else {
            $examPoints = 0;
        }

        $grades = $gradeRepository->findAll();

        $qualificationPoints = $pupil->getQualificationPoints();
        $examPointsSum = $examPoints*4;
        $points = $qualificationPoints + $examPointsSum;
        $pupilGrade = 0;
        $nextGrade = 0;
        $nextPoints = 0;
        $prevGrade = 0;
        $prevPoints = 0;

        foreach ($grades as $grade){
            if($points >= $grade->getMin() and $points <= $grade->getMax()) {
                $pupilGrade = $grade->getGrade();
            } elseif ($points > $grade->getMax()) {
                $prevGrade = $grade->getGrade();
                $prevPoints = $grade->getMax();
            } elseif ($points < $grade->getMin()) {
                $nextGrade = $grade->getGrade();
                $nextPoints = $grade->getMin();
                break;
            }
        }

        $diff = $examPointsSum + $nextPoints - $points;

        $extendedExams = [];

        foreach ($pupil->getExams() as $exam) {

            $x1 = ($exam->getExamPoints()*4 + + $prevPoints - $points) *3/4 - ($exam->getExamPoints()*2);
            $x2 = ($exam->getExamPoints()*4 + + $nextPoints - $points) *3/4 - ($exam->getExamPoints()*2);
            $x = round($x2);
            uasort($grades,new SortService('grade'));

            if ($exam->getExamNumber() <= 3) {
                $y = array_shift($grades);
                if($x <= 15 and $points < $y->getMin()) {
                    $eExam = new ExtendedExam();
                    $eExam->setExamNumber($exam->getExamNumber());
                    $eExam->setNeededExamPoints($x);
                    $eExam->setNeededExamPoints2($exam->getExamPoints());
                    $eExam->setSubject($exam->getSubject());
                    $eExam->setCriticalPoints(round($x1));
                    $extendedExams[] = $eExam;

                }
            }
        }


        return $this->render('pupil_crud/show.html.twig', [
            'pupil' => $pupil,
            'examPoints' => $examPointsSum,
            'grade' => $pupilGrade,
            'higherGrade' => $nextGrade,
            'extended_exams' => $extendedExams,
            'diff' => $diff + $qualificationPoints,
            'local_nav' => 'pupil',
            'side_nav' => 'overview',
        ]);
    }

    #[Route('/{id}/exam', name: 'exam_delete', methods: ['POST'])]
    public function deleteExam(Request $request, Exam $exam, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exam->getId(), $request->request->get('_token'))) {
            $entityManager->remove($exam);
            $entityManager->flush();

            $this->addFlash(
                'error',
                'Die '.$exam->getExamNumber().'. Prüfung wurde erfolgreich entfernt.'
            );
        }

        return $this->redirectToRoute('app_pupil_crud_show', ['id' => $exam->getPupil()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Pupil $pupil, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pupil->getId(), $request->request->get('_token'))) {
            foreach ($pupil->getExams() as $exam) {
                $entityManager->remove($exam);
            }
            $entityManager->remove($pupil);
            $entityManager->flush();

            $this->addFlash(
                'error',
                'Der Prüfling ' . $pupil . ' wurde erfolgreich entfernt.'
            );
        }

        return $this->redirectToRoute('app_pupil_crud_index',[], Response::HTTP_SEE_OTHER);
    }

}
