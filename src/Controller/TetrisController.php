<?php

namespace App\Controller;

use App\Attribute\EnsureVisitor;
use App\Entity\Tetris;
use App\Entity\Visitor;
use App\Repository\TetrisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[EnsureVisitor]
#[Route('/tetris')]
final class TetrisController extends AbstractController
{
    #[Route('/add', name: 'add_tetris', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $tetris = new Tetris();
        $visitor = $entityManager->getRepository(Visitor::class)->find($request->cookies->get('visitor_id'));

        $body = json_decode($request->getContent(), true);
        $tetris->setPkt($body['pkt']);
        $tetris->setFkVisitorId($visitor);

        $entityManager->persist($tetris);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    /**
     * GET /tetris/leaderboard/by-user?limit=10
     * Liefert die USER mit ihren jeweiligen Höchst-Scores (aggregiert via MAX(pkt)).
     */
    #[Route('/leaderboard/by-user', name: 'tetris_leaderboard_by_user', methods: ['GET'])]
    public function leaderboardByUser(Request $request, TetrisRepository $repo): JsonResponse
    {
        $limit = (int)($request->query->get('limit', 10));
        $limit = max(1, min($limit, 100));

        $rows = $repo->findTopUsersByMaxScore($limit);

        return $this->json([
            'type'  => 'users_max_score',
            'count' => count($rows),
            'data'  => $rows,
        ]);
    }

}
