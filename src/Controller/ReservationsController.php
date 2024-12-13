<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ReservationsController extends AbstractController
{
    #[Route('/api/reservations', methods: ['POST'])]
    public function addReservation(
        Request $request,
        SerializerInterface $serializer, 
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $jsonData = $request->getContent();
        $data = json_decode($request->getContent(), true);
        $date = new \DateTime($data['date']);

        if(!$data['date']){
            return new JsonResponse(['error' => 'Date introuvable'], JsonResponse::HTTP_CREATED ); 
        }

        if ($date <= new \DateTime()) {
            return new JsonResponse(['error' => 'Il est trop tard pour réserver'], JsonResponse::HTTP_BAD_REQUEST );
        }

        $sameDates = $reservationRepository->findBy(['date' => $date]); 

        foreach($sameDates as $sameDate){
            if($sameDate->getTimeSlot() == $data['timeSlot']){
                return new JsonResponse(['error' => 'plage horaire indisponible'], JsonResponse::HTTP_BAD_REQUEST );
            }
        }

        $reservation = $serializer->deserialize($jsonData, Reservation::class, 'json');
        $reservation->setRelations($user);

        $entityManager->persist($reservation);
        $entityManager->flush();

        $jsonReservation = $serializer->serialize($reservation, 'json', ['groups' => 'getReservation']);

        return new JsonResponse($jsonReservation, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/api/reservations', methods: ['GET'])]
    public function getReservations(
        ReservationRepository $reservationRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $reservations = $reservationRepository->findBy(['user' => $user]);
        $jsonReservations = $serializer->serialize($reservations, 'json', ['groups' => 'getReservation']);

        return new JsonResponse($jsonReservations, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/all/reservations', methods: ['GET'])]
    public function getAllReservations(
        ReservationRepository $reservationRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
       

        $reservations = $reservationRepository->findAll();
        $jsonReservations = $serializer->serialize($reservations, 'json', ['groups' => 'getReservation']);

        return new JsonResponse($jsonReservations, JsonResponse::HTTP_OK, [], true);
    }


}
