<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Reservation;
use App\Repository\RoomRepository;
use App\Repository\OwnerRepository;
use App\Repository\ReservationRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index")
     */
    public function index(RoomRepository $roomRepository, OwnerRepository $ownerRepository, ReservationRepository $reservationRepository, CommentaireRepository $commentaireRepository): Response
    {
        $nbreRooms = count($roomRepository->findAll());
        $nbreOwners = count($ownerRepository->findAll());
        $nbreReservations = count($reservationRepository->findBy(['confirmed' => false]));
        $nbreCommentaires = count($commentaireRepository->findBy(['status' => 'EN ATTENTE DE MODERATION']));
        
        return $this->render('admin/index.html.twig', [
            'nbreRooms' => $nbreRooms,
            'nbreOwners' => $nbreOwners,
            'nbreReservations' => $nbreReservations,
            'nbreCommentaires' => $nbreCommentaires
        ]);
    }

    /**
     * @Route("/rooms", name="admin_rooms")
     */
    public function rooms(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findAll();

        return $this->render('admin/rooms.html.twig', ['rooms' => $rooms]);
    }

    /**
     * @Route("/rooms/delete/{id}", name="admin_room_delete")
     */
    public function delete(Request $request, Room $room): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($room);
        $em->flush();

        $this->get('session')->getFlashBag()->add('message', 'Le Couette et Café a bien été supprimé.');
        return $this->redirectToRoute('admin_rooms', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/rooms/edit/{id}", name="admin_room_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Room $room): Response
    {
        //
    }

    /**
     * @Route("/reservations", name="admin_reservations")
     */
    public function reservations(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy(['confirmed' => false]);

        return $this->render('admin/reservations.html.twig', ['reservations' => $reservations]);
    }

    /**
     * @Route("/reservations/validate/{id}", name="admin_reservation_validate")
     */
    public function validate(Reservation $reservation): Response
    {
        $em = $this->getDoctrine()->getManager();
        $reservation->setConfirmed(true);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('message', 'La réservation a bien été validée.');
        return $this->redirectToRoute('admin_reservations');
    }

    /**
     * @Route("/reservations/cancel/{id}", name="admin_reservation_cancel")
     */
    public function cancel(Reservation $reservation): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($reservation);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('message', 'La réservation a bien été annulée.');
        return $this->redirectToRoute('admin_reservations');
    }

    /**
     * @Route("/moderation", name="admin_moderation")
     */
    public function moderation(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findBy(['status' => 'EN ATTENTE DE MODERATION']);

        return $this->render('admin/moderation.html.twig', ['commentaires' => $commentaires]);
    }
}
