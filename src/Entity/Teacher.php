<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Pupil::class)]
    private Collection $pupils;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $abbreviation = null;

    public function __construct()
    {
        $this->pupils = new ArrayCollection();
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

    /**
     * @return Collection<int, Pupil>
     */
    public function getPupils(): Collection
    {
        return $this->pupils;
    }

    public function addPupil(Pupil $pupil): static
    {
        if (!$this->pupils->contains($pupil)) {
            $this->pupils->add($pupil);
            $pupil->setTeacher($this);
        }

        return $this;
    }

    public function removePupil(Pupil $pupil): static
    {
        if ($this->pupils->removeElement($pupil)) {
            // set the owning side to null (unless already changed)
            if ($pupil->getTeacher() === $this) {
                $pupil->setTeacher(null);
            }
        }

        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): static
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }
}
