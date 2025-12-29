<?php

namespace App\Controller\Api;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/api/todos', name: 'api_todos_')]
class TodoController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(TodoRepository $todoRepository, Security $security): Response
    {
        $user = $security->getUser();
        $todos = $todoRepository->findBy(['user' => $user]);
        return $this->json($todos);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $data = json_decode($request->getContent(), true);

        $todo = new Todo();
        $todo->setTitle($data['title'] ?? '');
        $todo->setDescription($data['description'] ?? null);
        $todo->setIsCompleted(false);
        $todo->setCreatedAt(new \DateTime());
        $todo->setUser($security->getUser());

        $em->persist($todo);
        $em->flush();

        return $this->json($todo, 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Todo $todo, Security $security): Response
    {
        if ($todo->getUser() !== $security->getUser()) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        return $this->json($todo);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Todo $todo, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($todo->getUser() !== $security->getUser()) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $todo->setTitle($data['title'] ?? $todo->getTitle());
        $todo->setDescription($data['description'] ?? $todo->getDescription());
        $todo->setIsCompleted($data['isCompleted'] ?? $todo->isIsCompleted());

        $em->flush();

        return $this->json($todo);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Todo $todo, EntityManagerInterface $em, Security $security): Response
    {
        if ($todo->getUser() !== $security->getUser()) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $em->remove($todo);
        $em->flush();

        return $this->json(null, 204);
    }
}
