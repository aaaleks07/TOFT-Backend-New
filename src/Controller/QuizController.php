<?php

namespace App\Controller;

use App\Attribute\EnsureVisitor;
use App\Entity\Quiz;
use App\Entity\Visitor;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quiz')]
final class QuizController extends AbstractController
{
    #[Route('/add', name: 'add_quiz', methods: ['POST'])]
    #[EnsureVisitor]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $quiz = new Quiz();
        $visitor = $entityManager->getRepository(Visitor::class)->find($request->cookies->get('visitor_id'));

        $body = json_decode($request->getContent(), true);
        $quiz->setName($body['name']);
        $quiz->setPkt($body['pkt']);
        $quiz->setFkVisitorId($visitor);

        $entityManager->persist($quiz);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
