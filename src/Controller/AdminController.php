<?php

namespace App\Controller;


use App\Repository\RoomRepository;
use App\Repository\OwnerRepository;
use App\Repository\ReservationRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
