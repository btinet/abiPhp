<?php

namespace App\Service;

use App\Entity\Exam;
use App\Entity\Pupil;
use Doctrine\ORM\EntityManagerInterface;

class PupilExamService
{

    public function getExamPointSum(EntityManagerInterface $entityManager, Pupil $pupil)
    {
        $result =  $entityManager->getRepository(Exam::class)->sumExamPoints($pupil->getId());
        if(is_array($result)) {
            return array_pop($result)[0];
        }
        return null;
    }

}