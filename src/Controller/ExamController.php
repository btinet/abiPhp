<?php

namespace App\Controller;

use App\Entity\ExtendedExam;
use App\Entity\Grade;
use App\Repository\ExamRepository;
use App\Repository\GradeRepository;
use App\Repository\PupilRepository;
use App\Service\SortService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/exam', name: 'app_exam_')]
class ExamController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(PupilRepository $pupilRepository, ExamRepository $examRepository, GradeRepository $gradeRepository): Response
    {
        $extendedExams = [];

        foreach ($pupilRepository->findAll() as $pupil) {

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



            foreach ($pupil->getExams() as $exam) {

                $x1 = ($exam->getExamPoints()*4 + + $prevPoints - $points) *3/4 - ($exam->getExamPoints()*2);
                $x2 = ($exam->getExamPoints()*4 + + $nextPoints - $points) *3/4 - ($exam->getExamPoints()*2);
                $x = round($x2);
                uasort($grades,new SortService('grade'));

                if ($exam->getExamNumber() <= 3) {
                    $y = array_shift($grades);
                    if($x <= 15 and $points < $y->getMin()) {
                        $eExam = new ExtendedExam();
                        $eExam->setPupil($pupil);
                        $eExam->setNextGrade($nextGrade);
                        $eExam->setExamNumber($exam->getExamNumber());
                        $eExam->setNeededExamPoints($x);
                        $eExam->setNeededExamPoints2($exam->getExamPoints());
                        $eExam->setSubject($exam->getSubject());
                        $eExam->setCriticalPoints(round($x1));
                        $extendedExams[] = $eExam;

                    }
                }
            }

        }

        return $this->render('exam/index.html.twig', [
            'exams' => $extendedExams,
            'local_nav' => 'exam'
        ]);

    }

}