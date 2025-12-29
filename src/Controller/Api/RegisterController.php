<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/register', name: 'api_register', methods: ['POST'])]
class RegisterController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email'], $data['password'], $data['name'])) {
            return new JsonResponse(['error' => 'Email, password and name required'], Response::HTTP_BAD_REQUEST);
        }
        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);
        if (method_exists($user, 'setName')) {
            $user->setName($data['name']);
        }
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );
        $em->persist($user);
        $em->flush();
        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => method_exists($user, 'getName') ? $user->getName() : null,
        ], Response::HTTP_CREATED);
    }
}
