<?php
// src/Repository/JokeVoteRepository.php

namespace App\Repository;

use App\Entity\Joke;
use App\Entity\JokeVote;
use App\Entity\Visitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JokeVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JokeVote::class);
    }

    public function findOneByJokeAndVisitor(Joke $joke, Visitor $visitor): ?JokeVote
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.joke_id = :joke')
            ->andWhere('v.visitor_id = :visitor')
            ->setParameter('joke', $joke)
            ->setParameter('visitor', $visitor)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
