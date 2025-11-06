<?php
// src/Repository/JokeRepository.php

namespace App\Repository;

use App\Entity\Joke;
use App\Enum\VoteValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JokeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Joke::class);
    }

    /**
     * Liefert alle Witze mit aggregierten Votes (Summe aus +1/-1).
     * Rückgabeformat: array<array{id:int, text:string, votes:int}>
     */
    public function findAllWithScore(): array
    {
        // Enum-Vergleich in DQL: vergleiche direkt mit Parameter vom Enum-Typ
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.jokeVotes', 'v')
            ->select('j.id AS id, j.text AS text')
            ->addSelect(
                'COALESCE(SUM(CASE WHEN v.vote = :up THEN 1 WHEN v.vote = :down THEN -1 ELSE 0 END), 0) AS votes'
            )
            ->groupBy('j.id')
            ->orderBy('votes', 'DESC')
            ->addOrderBy('j.id', 'ASC')
            ->setParameter('up', VoteValue::UP)
            ->setParameter('down', VoteValue::DOWN);

        // numerische Indizes -> assoziativ umwandeln via getScalarResult()
        return $qb->getQuery()->getScalarResult();
    }

    /**
     * Aggregierter Score für einen einzelnen Witz.
     */
    public function getScoreForJoke(Joke $joke): int
    {
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.jokeVotes', 'v')
            ->select(
                'COALESCE(SUM(CASE WHEN v.vote = :up THEN 1 WHEN v.vote = :down THEN -1 ELSE 0 END), 0) AS score'
            )
            ->where('j = :joke')
            ->setParameter('joke', $joke)
            ->setParameter('up', VoteValue::UP)
            ->setParameter('down', VoteValue::DOWN);

        $res = $qb->getQuery()->getSingleScalarResult();

        return (int)$res;
    }

    public function findOneByTextCaseInsensitive(string $text): ?Joke
    {
        return $this->createQueryBuilder('j')
            ->andWhere('LOWER(j.text) = LOWER(:t)')
            ->setParameter('t', $text)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
