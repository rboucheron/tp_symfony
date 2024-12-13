<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;

class AdminController extends AbstractController
{
    #[Route('/api/manage/users', methods: ['GET'])]
    public function getUsers(
        UserRepository $userRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepository->findAll();
        $jsonUsers = $serializer->serialize($users, 'json', ['groups' => 'getUser']);

        return new JsonResponse($jsonUsers, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/manage/users/{id}', methods: ['GET'])]
    public function getSingleUser(
        int $id, 
        UserRepository $userRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'utilisateur introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUser']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/manage/users', methods: ['POST'])]
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManager, 
        SerializerInterface $serializer
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');

        $entityManager->persist($user);
        $entityManager->flush();


        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUser']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/api/manage/users/{id}', methods: ['PUT'])]
    public function updateUser(
        int $id, 
        Request $request, 
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager, 
        SerializerInterface $serializer
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'utilisateur introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = $request->getContent();
        $updateUser = $serializer->deserialize($data, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        $entityManager->flush();

        $jsonUser = $serializer->serialize($updateUser, 'json', ['groups' => 'getUser']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/api/manage/users/{id}', methods: ['DELETE'])]
    public function deleteUser(
        int $id, 
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'utilisateur introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'utilisateur supprimer'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/manage/reservation/{id}', methods: ['DELETE'])]
    public function deleteReservation(
        int $id,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager
    ) : JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $reservation = $reservationRepository->find($id);

        if (!$reservation) {
            return new JsonResponse(['message' => 'reservation introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($reservation);
        $entityManager->flush();

        return new JsonResponse(['message' => 'reservation supprimer'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/manage/reservation/{id}', methods: ['PUT'])]
    public function updateReservation(
        int $id,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        Serializer $serializer,
    ) : JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $reservation = $reservationRepository->find($id);
        $data = $request->getContent();

        if (!$reservation) {
            return new JsonResponse(['message' => 'reservation introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        $updateReservation = $serializer->deserialize($data, Reservation::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $reservation]);
        $entityManager->flush();
        $jsonReservation = $serializer->serialize($updateReservation, 'json', ['groups' => 'getReservation']);


        return new JsonResponse($jsonReservation, JsonResponse::HTTP_CREATED, [], true);
    }


}