<?php

namespace App\Entity;

use App\Enum\VoteValue;
use App\Repository\JokeVoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JokeVoteRepository::class)]
class JokeVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'jokeVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Joke $joke_id = null;

    #[ORM\Column(enumType: VoteValue::class)]
    private ?VoteValue $vote = null;

    #[ORM\ManyToOne(inversedBy: 'jokeVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Visitor $visitor_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJokeId(): ?Joke
    {
        return $this->joke_id;
    }

    public function setJokeId(?Joke $joke_id): static
    {
        $this->joke_id = $joke_id;

        return $this;
    }

    public function getVote(): ?VoteValue
    {
        return $this->vote;
    }

    public function setVote(VoteValue $vote): static
    {
        $this->vote = $vote;

        return $this;
    }

    public function getVisitorId(): ?Visitor
    {
        return $this->visitor_id;
    }

    public function setVisitorId(?Visitor $visitor_id): static
    {
        $this->visitor_id = $visitor_id;

        return $this;
    }
}
