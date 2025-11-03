<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Visitor $fk_visitor_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $quiz_title = null;

    #[ORM\Column]
    private array $questions = [];

    #[ORM\OneToOne(mappedBy: 'quiz_id', cascade: ['persist', 'remove'])]
    private ?CompletedQuiz $completedQuiz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkVisitorId(): ?Visitor
    {
        return $this->fk_visitor_id;
    }

    public function setFkVisitorId(?Visitor $fk_visitor_id): static
    {
        $this->fk_visitor_id = $fk_visitor_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuizTitle(): ?string
    {
        return $this->quiz_title;
    }

    public function setQuizTitle(string $quiz_title): static
    {
        $this->quiz_title = $quiz_title;

        return $this;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): static
    {
        $this->questions = $questions;

        return $this;
    }

    public function getCompletedQuiz(): ?CompletedQuiz
    {
        return $this->completedQuiz;
    }

    public function setCompletedQuiz(CompletedQuiz $completedQuiz): static
    {
        // set the owning side of the relation if necessary
        if ($completedQuiz->getQuizId() !== $this) {
            $completedQuiz->setQuizId($this);
        }

        $this->completedQuiz = $completedQuiz;

        return $this;
    }
}
