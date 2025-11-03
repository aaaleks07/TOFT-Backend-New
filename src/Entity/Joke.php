<?php

namespace App\Entity;

use App\Repository\JokeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JokeRepository::class)]
class Joke
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    /**
     * @var Collection<int, JokeVote>
     */
    #[ORM\OneToMany(targetEntity: JokeVote::class, mappedBy: 'joke_id')]
    private Collection $jokeVotes;

    public function __construct()
    {
        $this->vote = new ArrayCollection();
        $this->jokeVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection<int, JokeVote>
     */
    public function getVote(): Collection
    {
        return $this->vote;
    }

    public function addVote(JokeVote $vote): static
    {
        if (!$this->vote->contains($vote)) {
            $this->vote->add($vote);
            $vote->setJokeId($this);
        }

        return $this;
    }

    public function removeVote(JokeVote $vote): static
    {
        if ($this->vote->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getJokeId() === $this) {
                $vote->setJokeId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, JokeVote>
     */
    public function getJokeVotes(): Collection
    {
        return $this->jokeVotes;
    }

    public function addJokeVote(JokeVote $jokeVote): static
    {
        if (!$this->jokeVotes->contains($jokeVote)) {
            $this->jokeVotes->add($jokeVote);
            $jokeVote->setJokeId($this);
        }

        return $this;
    }

    public function removeJokeVote(JokeVote $jokeVote): static
    {
        if ($this->jokeVotes->removeElement($jokeVote)) {
            // set the owning side to null (unless already changed)
            if ($jokeVote->getJokeId() === $this) {
                $jokeVote->setJokeId(null);
            }
        }

        return $this;
    }
}
