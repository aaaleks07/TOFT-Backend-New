<?php
// src/Controller/JokeController.php

namespace App\Controller;

use App\Attribute\EnsureVisitor;
use App\Entity\Joke;
use App\Entity\JokeVote;
use App\Entity\Visitor;
use App\Enum\VoteValue;
use App\Repository\JokeRepository;
use App\Repository\JokeVoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/jokes')]
final class JokesController extends AbstractController
{
    #[Route('/add', name: 'jokes_create', methods: ['POST'])]
    #[EnsureVisitor]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        JokeRepository $jokeRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        $raw = (string)($data['text'] ?? '');
        $text = preg_replace('/\s+/u', ' ', trim($raw)); // trim + Whitespace normalisieren

        if ($text === '') {
            return new JsonResponse(['error' => 'Feld "text" ist erforderlich.'], 400);
        }

        // Case-insensitive Prüfung (DB-agnostisch; s. Abschnitt 2/3 für "richtig" hart)
        $existing = $jokeRepo->findOneByTextCaseInsensitive($text);
        if ($existing) {
            return new JsonResponse(['error' => 'Witz existiert bereits'], 409);
        }

        $joke = new Joke();
        $joke->setText($text);

        $em->persist($joke);
        $em->flush();

        return new JsonResponse([
            'id'    => $joke->getId(),
            'text'  => $joke->getText(),
            'votes' => 0,
        ], 201);
    }


    #[Route('/vote/{id}', name: 'jokes_vote', methods: ['POST'])]
    #[EnsureVisitor]
    public function vote(
        Joke $joke,
        Request $request,
        EntityManagerInterface $em,
        JokeVoteRepository $voteRepo,
        JokeRepository $jokeRepo
    ): JsonResponse {
        // Visitor aus Cookie (EnsureVisitor setzt/garantiert ihn)
        /** @var Visitor|null $visitor */
        $visitor = $em->getRepository(Visitor::class)->find($request->cookies->get('visitor_id'));
        if (!$visitor) {
            return new JsonResponse(['error' => 'Visitor nicht gefunden.'], 400);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $voteVal = $data['vote'] ?? null;

        if (!in_array($voteVal, [1, -1], true)) {
            return new JsonResponse(['error' => 'Feld "vote" muss 1 oder -1 sein.'], 400);
        }

        // Prüfen, ob dieser Visitor schon für diesen Witz abgestimmt hat
        $existing = $voteRepo->findOneByJokeAndVisitor($joke, $visitor);
        if ($existing) {
            return new JsonResponse(['error' => 'Du hast bereits abgestimmt'], 409);
        }

        // Neues Vote anlegen
        $vote = new JokeVote();
        $vote->setJokeId($joke);
        $vote->setVisitorId($visitor);
        $vote->setVote($voteVal === 1 ? VoteValue::UP : VoteValue::DOWN);

        $em->persist($vote);
        $em->flush();

        $score = $jokeRepo->getScoreForJoke($joke);

        return new JsonResponse([
            'id'    => $joke->getId(),
            'votes' => $score,
        ], 200);
    }

    #[Route('', name: 'jokes_index', methods: ['GET'])]
    public function index(JokeRepository $jokeRepo): JsonResponse
    {
        $rows = $jokeRepo->findAllWithScore();

        return new JsonResponse($rows, 200);
    }
}
