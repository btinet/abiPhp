<?php

namespace App\Entity;

class ExtendedExam
{

    private Pupil $pupil;
    private float $nextGrade;
    private int $examNumber;
    private string $subject;
    private int $neededExamPoints;
    private int $neededExamPoints2;
    private mixed $criticalPoints = -1;

    /**
     * @return Pupil
     */
    public function getPupil(): Pupil
    {
        return $this->pupil;
    }

    /**
     * @param Pupil $pupil
     */
    public function setPupil(Pupil $pupil): void
    {
        $this->pupil = $pupil;
    }

    /**
     * @return float
     */
    public function getNextGrade(): float
    {
        return $this->nextGrade;
    }

    /**
     * @param float $nextGrade
     */
    public function setNextGrade(float $nextGrade): void
    {
        $this->nextGrade = $nextGrade;
    }



    /**
     * @return int
     */
    public function getExamNumber(): int
    {
        return $this->examNumber;
    }

    /**
     * @param int $examNumber
     */
    public function setExamNumber(int $examNumber): void
    {
        $this->examNumber = $examNumber;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return int
     */
    public function getNeededExamPoints(): int
    {
        return $this->neededExamPoints;
    }

    /**
     * @param int $neededExamPoints
     */
    public function setNeededExamPoints(int $neededExamPoints): void
    {
        $this->neededExamPoints = $neededExamPoints;
    }

    /**
     * @return int
     */
    public function getNeededExamPoints2(): int
    {
        return $this->neededExamPoints2;
    }

    /**
     * @param int $neededExamPoints2
     */
    public function setNeededExamPoints2(int $neededExamPoints2): void
    {
        $this->neededExamPoints2 = $neededExamPoints2;
    }

    /**
     * @return mixed
     */
    public function getCriticalPoints(): mixed
    {
        if($this->criticalPoints >= 0) {
            return '<span class="rounded color-bg-danger color-fg-danger text-small px-2 py-1">'.$this->criticalPoints.' Notenpunkt(e)</span>';
        } else {
            return '<span class="rounded color-bg-open color-fg-open text-small px-2 py-1">ohne Risiko</span>';
        }

    }

    /**
     * @param mixed|string $criticalPoints
     */
    public function setCriticalPoints(mixed $criticalPoints): void
    {
        $this->criticalPoints = $criticalPoints;
    }



}
