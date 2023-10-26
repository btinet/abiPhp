<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Entity\ExtendedExam;
use App\Entity\Pupil;
use App\Form\ExamType;
use App\Form\PupilType;
use App\Repository\ExamRepository;
use App\Repository\GradeRepository;
use App\Repository\PupilRepository;
use App\Repository\TeacherRepository;
use App\Service\SortService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Math;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;

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

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
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
            'pupil' => $pupil,
            'form' => $form,
            'local_nav' => 'pupil'
        ]);
    }

    #[Route('{id}/exam/add', name: 'exam_add', methods: ['GET', 'POST'])]
    public function examAdd(Request $request, EntityManagerInterface $entityManager, Pupil $pupil): Response
    {
        if($pupil->getExams()->count() == 5) {
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

            return $this->redirectToRoute('app_pupil_crud_exam_add', ['id' => $pupil->getId()], Response::HTTP_SEE_OTHER);
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

            return $this->redirectToRoute('app_pupil_crud_index', [], Response::HTTP_SEE_OTHER);
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
}
