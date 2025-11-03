<?php

namespace App\Controller;

use App\Attribute\EnsureVisitor;
use App\Entity\CompletedQuiz;
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
    #[Route('/getQuiz', methods: ['GET'])]
    public function getQuiz(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $quizId = $req->query->get('quiz_id');
        $quiz = $em->getRepository(Quiz::class)->find($quizId);

        if (!$quiz) {
            return new JsonResponse(['error' => 'Quiz not found'], 404);
        }

        return new JsonResponse([
            'quiz' => [
                'quiz_id' => $quiz->getId(),
                'quiz_title' => $quiz->getQuizTitle(),
                'questions' => $quiz->getQuestions(),
            ],
        ]);
    }

    #[Route('/submitQuiz', methods: ['POST'])]
    public function submitQuiz(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        if (!$data || !isset($data['visitor_id'], $data['quiz_id'], $data['score'], $data['max_score'])) {
            return new JsonResponse(['error' => 'Missing fields'], 400);
        }

        // Prüfen, ob der User das Quiz bereits abgeschlossen hat
        $existing = $em->getRepository(CompletedQuiz::class)->findOneBy([
            'visitorId' => $data['visitor_id'],
            'quizId' => $data['quiz_id']
        ]);

        if ($existing) {
            return new JsonResponse(['error' => 'Quiz bereits abgeschlossen'], 400);
        }

        $completed = new CompletedQuiz();
        $completed->setVisitorId($data['visitor_id']);
        $completed->setQuizId($data['quiz_id']);
        $completed->setScore($data['score']);
        $completed->setMaxScore($data['max_score']);

        $em->persist($completed);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }


    #[Route('/getCompletedQuizzes', methods: ['GET'])]
    public function getCompletedQuizzes(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $visitorId = $req->query->get('visitor_id');
        $completed = $em->getRepository(CompletedQuiz::class)->findBy(['visitorId' => $visitorId]);

        return new JsonResponse([
            'quizzes' => array_map(function ($c) use ($em) {
                $quiz = $em->getRepository(Quiz::class)->find($c->getQuizId());
                return [
                    'quiz_id' => $c->getQuizId(),
                    'quiz_title' => $quiz ? $quiz->getQuizTitle() : 'Unbekannt',
                    'score' => $c->getScore(),
                    'max_score' => $c->getMaxScore(),
                    'completed_at' => $c->getCompletedAt()->format('Y-m-d H:i:s'),
                ];
            }, $completed),
        ]);
    }


    #[Route('/getUserRank', methods: ['GET'])]
    public function getUserRank(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $visitorId = $req->query->get('visitor_id');
        if (!$visitorId) {
            return new JsonResponse(['error' => 'visitor_id is required'], 400);
        }

        $connection = $em->getConnection();

        // Alle Spieler nach Durchschnittspunkte sortieren
        $sql = "SELECT visitor_id, AVG(score / max_score) * 100 AS avg_percent
            FROM completed_quiz
            GROUP BY visitor_id
            ORDER BY avg_percent DESC";
        $results = $connection->executeQuery($sql)->fetchAllAssociative();

        $rank = null;
        foreach ($results as $index => $row) {
            if ($row['visitor_id'] === $visitorId) {
                $rank = $index + 1; // Platz = Index + 1
                break;
            }
        }

        if ($rank === null) {
            return new JsonResponse(['error' => 'User has not completed any quizzes'], 404);
        }

        return new JsonResponse(['rank' => $rank]);
    }


    // -----------------------
    // NEW ROUTES
    // -----------------------

    // GET: Alle Quizzes und CompletedQuizzes
    #[Route('/allQuizzesAndCompleted', methods: ['GET'])]
    public function allQuizzesAndCompleted(EntityManagerInterface $em): JsonResponse
    {
        $quizzes = $em->getRepository(Quiz::class)->findAll();
        $completedQuizzes = $em->getRepository(CompletedQuiz::class)->findAll();

        return new JsonResponse([
            'quizzes' => array_map(fn($q) => [
                'quiz_id' => $q->getId(),
                'quiz_title' => $q->getQuizTitle(),
                'questions' => $q->getQuestions(),
            ], $quizzes),
            'completed_quizzes' => array_map(fn($c) => [
                'id' => $c->getId(),
                'visitor_id' => $c->getVisitorId(),
                'quiz_id' => $c->getQuizId(),
                'score' => $c->getScore(),
                'max_score' => $c->getMaxScore(),
                'completed_at' => $c->getCompletedAt()->format('Y-m-d H:i:s'),
            ], $completedQuizzes),
        ]);
    }

    // -----------------------
    // CRUD Quiz
    // -----------------------
    #[Route('/quiz/create', methods: ['POST'])]
    public function createQuiz(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        if (!isset($data['quiz_title'], $data['questions'])) {
            return new JsonResponse(['error' => 'Missing fields'], 400);
        }

        $quiz = new Quiz();
        $quiz->setQuizTitle($data['quiz_title']);
        $quiz->setQuestions($data['questions']);
        $em->persist($quiz);
        $em->flush();

        return new JsonResponse(['success' => true, 'quiz_id' => $quiz->getId()]);
    }

    #[Route('/quiz/update/{id}', methods: ['PUT'])]
    public function updateQuiz(int $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $quiz = $em->getRepository(Quiz::class)->find($id);
        if (!$quiz) {
            return new JsonResponse(['error' => 'Quiz not found'], 404);
        }

        $data = json_decode($req->getContent(), true);
        if (isset($data['quiz_title'])) $quiz->setQuizTitle($data['quiz_title']);
        if (isset($data['questions'])) $quiz->setQuestions($data['questions']);

        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/quiz/delete/{id}', methods: ['DELETE'])]
    public function deleteQuiz(int $id, EntityManagerInterface $em): JsonResponse
    {
        $quiz = $em->getRepository(Quiz::class)->find($id);
        if (!$quiz) {
            return new JsonResponse(['error' => 'Quiz not found'], 404);
        }

        $em->remove($quiz);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    // -----------------------
    // CRUD CompletedQuiz
    // -----------------------
    #[Route('/completedQuiz/delete/{id}', methods: ['DELETE'])]
    public function deleteCompletedQuiz(int $id, EntityManagerInterface $em): JsonResponse
    {
        $completed = $em->getRepository(CompletedQuiz::class)->find($id);
        if (!$completed) {
            return new JsonResponse(['error' => 'CompletedQuiz not found'], 404);
        }

        $em->remove($completed);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/completedQuiz/update/{id}', methods: ['PUT'])]
    public function updateCompletedQuiz(int $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $completed = $em->getRepository(CompletedQuiz::class)->find($id);
        if (!$completed) {
            return new JsonResponse(['error' => 'CompletedQuiz not found'], 404);
        }

        $data = json_decode($req->getContent(), true);
        if (isset($data['score'])) $completed->setScore($data['score']);
        if (isset($data['max_score'])) $completed->setMaxScore($data['max_score']);

        $em->flush();
        return new JsonResponse(['success' => true]);
    }
}
