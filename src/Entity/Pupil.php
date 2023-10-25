<?php

namespace App\Entity;

use App\Repository\PupilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PupilRepository::class)]
class Pupil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\ManyToOne(inversedBy: 'pupils')]
    private ?Teacher $teacher = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $examDate = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $qualificationPoints = null;

    #[ORM\OneToMany(mappedBy: 'pupil', targetEntity: Exam::class)]
    private Collection $exams;

    public function __construct()
    {
        $this->exams = new ArrayCollection();
    }

    public function __toString(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getExamDate(): ?\DateTimeInterface
    {
        return $this->examDate;
    }

    public function setExamDate($examDate): static
    {
        if(is_int($examDate)) {
            $this->examDate = date_create("$examDate-01-01");
        } else {
            $this->examDate = $examDate;
        }
        return $this;
    }

    public function getQualificationPoints(): ?string
    {
        return $this->qualificationPoints;
    }

    public function setQualificationPoints(?string $qualificationPoints): static
    {
        $this->qualificationPoints = $qualificationPoints;

        return $this;
    }

    /**
     * @return Collection<int, Exam>
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exam $exam): static
    {
        if (!$this->exams->contains($exam)) {
            $this->exams->add($exam);
            $exam->setPupil($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): static
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)
            if ($exam->getPupil() === $this) {
                $exam->setPupil(null);
            }
        }

        return $this;
    }
}
