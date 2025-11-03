<?php

namespace App\Entity;

use App\Repository\VisitorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: VisitorRepository::class)]
class Visitor
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'fk_visitor_id')]
    private Collection $quizzes;

    /**
     * @var Collection<int, Snake>
     */
    #[ORM\OneToMany(targetEntity: Snake::class, mappedBy: 'fk_visitor_id')]
    private Collection $snakes;

    /**
     * @var Collection<int, Tetris>
     */
    #[ORM\OneToMany(targetEntity: Tetris::class, mappedBy: 'fk_visitor_id')]
    private Collection $tetris;

    #[ORM\OneToOne(mappedBy: 'visitor_id', cascade: ['persist', 'remove'])]
    private ?CompletedQuiz $completedQuiz = null;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->snakes = new ArrayCollection();
        $this->tetris = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setFkVisitorId($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getFkVisitorId() === $this) {
                $quiz->setFkVisitorId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Snake>
     */
    public function getSnakes(): Collection
    {
        return $this->snakes;
    }

    public function addSnake(Snake $snake): static
    {
        if (!$this->snakes->contains($snake)) {
            $this->snakes->add($snake);
            $snake->setFkVisitorId($this);
        }

        return $this;
    }

    public function removeSnake(Snake $snake): static
    {
        if ($this->snakes->removeElement($snake)) {
            // set the owning side to null (unless already changed)
            if ($snake->getFkVisitorId() === $this) {
                $snake->setFkVisitorId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tetris>
     */
    public function getTetris(): Collection
    {
        return $this->tetris;
    }

    public function addTetri(Tetris $tetri): static
    {
        if (!$this->tetris->contains($tetri)) {
            $this->tetris->add($tetri);
            $tetri->setFkVisitorId($this);
        }

        return $this;
    }

    public function removeTetri(Tetris $tetri): static
    {
        if ($this->tetris->removeElement($tetri)) {
            // set the owning side to null (unless already changed)
            if ($tetri->getFkVisitorId() === $this) {
                $tetri->setFkVisitorId(null);
            }
        }

        return $this;
    }

    public function getCompletedQuiz(): ?CompletedQuiz
    {
        return $this->completedQuiz;
    }

    public function setCompletedQuiz(CompletedQuiz $completedQuiz): static
    {
        // set the owning side of the relation if necessary
        if ($completedQuiz->getVisitorId() !== $this) {
            $completedQuiz->setVisitorId($this);
        }

        $this->completedQuiz = $completedQuiz;

        return $this;
    }
}
