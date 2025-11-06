<?php

namespace App\Controller;

use App\Attribute\EnsureVisitor;
use App\Entity\Quiz;
use App\Entity\Snake;
use App\Entity\Visitor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/snake')]
#[EnsureVisitor]
final class SnakeController extends AbstractController
{
    #[Route('/add', name: 'add_snake', methods: ['POST'])]
    #[EnsureVisitor]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $snake = new Snake();
        $visitor = $entityManager->getRepository(Visitor::class)->find($request->cookies->get('visitor_id'));

        $body = json_decode($request->getContent(), true);
        $snake->setPkt($body['pkt']);
        $snake->setFkVisitorId($visitor);

        $entityManager->persist($snake);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
