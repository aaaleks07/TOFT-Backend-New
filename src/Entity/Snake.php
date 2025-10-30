<?php

namespace App\Entity;

use App\Repository\SnakeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SnakeRepository::class)]
class Snake
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'snakes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Visitor $fk_visitor_id = null;

    #[ORM\Column]
    private ?int $pkt = null;

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

    public function getPkt(): ?int
    {
        return $this->pkt;
    }

    public function setPkt(int $pkt): static
    {
        $this->pkt = $pkt;

        return $this;
    }
}
