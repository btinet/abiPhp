<?php

namespace App\Entity;

use App\Repository\ExamRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExamRepository::class)]
class Exam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $examNumber = null;

    #[ORM\Column]
    private ?int $examPoints = null;

    #[ORM\ManyToOne(inversedBy: 'exams')]
    private ?Pupil $pupil = null;

    #[ORM\ManyToOne(inversedBy: 'exams')]
    private ?ExamSubject $subject = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExamNumber(): ?int
    {
        return $this->examNumber;
    }

    public function setExamNumber(int $examNumber): static
    {
        $this->examNumber = $examNumber;

        return $this;
    }

    public function getExamPoints(): ?int
    {
        return $this->examPoints;
    }

    public function setExamPoints(int $examPoints): static
    {
        $this->examPoints = $examPoints;

        return $this;
    }

    public function getPupil(): ?Pupil
    {
        return $this->pupil;
    }

    public function setPupil(?Pupil $pupil): static
    {
        $this->pupil = $pupil;

        return $this;
    }

    public function getSubject(): ?ExamSubject
    {
        return $this->subject;
    }

    public function setSubject(?ExamSubject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }
}
