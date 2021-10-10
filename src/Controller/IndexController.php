<?php

namespace App\Controller;

use \DateTime;
use App\Entity\Room;
use App\Entity\Region;
use App\Entity\Reservation;
use App\Entity\Commentaire;
use App\Repository\RoomRepository;
use App\Repository\RegionRepository;
use App\Repository\ReservationRepository;
use App\Repository\CommentaireRepository;
use App\Form\ReservationType;
use App\Form\CommentaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(RegionRepository $regionRepository): Response
    {
        $regions = $regionRepository->findAll();

        return $this->render('index/index.html.twig', [
            'regions' => $regions,
        ]);
    }

    /**
     * @Route("/{region}/rooms", name="home_room", methods={"GET"})
     */
    public function rooms(string $region, RegionRepository $regionRepository): Response
    {
        $region = $regionRepository->findOneBy(['name' => $region]);
        $rooms = $region->getRooms();
        // On a récupéré toutes les Rooms, on va trier à l'aide de isFree pour récupérer seulement les Rooms libres aujourd'hui

        $freeRooms = array();
        foreach($rooms as $room) {
            if ($room->isFree()) {
                $freeRooms[] = $room;
            }
        }
        return $this->render('index/rooms.html.twig', [ 'rooms' => $freeRooms ]);
    }

    /**
     * @Route("/rooms/{id}", name="home_room_show", methods={"GET", "POST"})
     */
    public function show(Room $room, Request $request, CommentaireRepository $commentaireRepository): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setDate(new DateTime());
            $commentaire->setStatus("EN ATTENTE DE CONFIRMATION");
            $commentaire->setAuthor($this->getUser()->getClient());
            $commentaire->setRoom($room);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('message', 'Votre commentaire est en cours de modération et sera publié sous peu.');
            return $this->redirectToRoute('home_room_show', ['id' => $room->getId()]);
        }

        $commentaires = $commentaireRepository->findBy(['room' => $room]);
        return $this->render('index/show.html.twig', ['room' => $room, 'form' => $form->createView(), 'commentaires' => $commentaires]);
    }

    /**
     * @Route("/reservation/{id}", name="home_reservation", methods={"GET", "POST"})
     */
    public function reserve(Room $room, Request $request, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setClient($this->getUser()->getClient());
            $reservation->setRoom($room);
            $reservation->setConfirmed(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('message', 'Votre réservation est en cours de traitement');
            return $this->redirectToRoute('index');
        }

        // Collecte des réservations à venir avec une fonction définie dans le repository
        $upcomingReservations = $reservationRepository->findUpcomingReservations($room);
        return $this->render('index/reservation.html.twig', ['room' => $room, 'form' => $form->createView(), 'reservations' => $upcomingReservations]);
    }

    /**
     * @Route("/reservations", name="home_user_reservations")
     */
    public function reservations(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy(['client' => $this->getUser()->getClient()]);

        return $this->render('index/user_reservations.html.twig', ['reservations' => $reservations]);
    }
}
