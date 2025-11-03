<?php

namespace App\Entity;

use App\Repository\CompletedQuizRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompletedQuizRepository::class)]
class CompletedQuiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'completedQuiz', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Visitor $visitor_id = null;

    #[ORM\OneToOne(inversedBy: 'completedQuiz', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz_id = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?\DateTime $completed_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisitorId(): ?Visitor
    {
        return $this->visitor_id;
    }

    public function setVisitorId(Visitor $visitor_id): static
    {
        $this->visitor_id = $visitor_id;

        return $this;
    }

    public function getQuizId(): ?Quiz
    {
        return $this->quiz_id;
    }

    public function setQuizId(Quiz $quiz_id): static
    {
        $this->quiz_id = $quiz_id;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completed_at;
    }

    public function setCompletedAt(\DateTime $completed_at): static
    {
        $this->completed_at = $completed_at;

        return $this;
    }
}
