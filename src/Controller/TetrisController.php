<?php

namespace App\Controller;

use App\Attribute\EnsureVisitor;
use App\Entity\Tetris;
use App\Entity\Visitor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tetris')]
final class TetrisController extends AbstractController
{
    #[Route('/add', name: 'add_tetris', methods: ['POST'])]
    #[EnsureVisitor]
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
}
