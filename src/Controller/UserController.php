<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/api/manage/account', methods: ['GET'])]
    public function showAccount(
        SerializerInterface $serializer,
    ) : JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUser']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);

    }

    #[Route('/api/manage/account', methods: ['PUT'])]
    public function manageAccount(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $entityManager
    ) : JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $data = $request->getContent();
        $updateUser = $serializer->deserialize($data, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        $entityManager->flush();

        $jsonUser = $serializer->serialize($updateUser, 'json', ['groups' => 'getUser']);
        
        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }


}
