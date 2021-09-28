<?php

namespace App\Controller;

use App\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnoncesController extends AbstractController
{
    /**
     * @Route("/rooms/", name="room_index")
     * @Route("/rooms/list", name="room_list")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rooms = $em->getRepository(Room::Class)->findAll();
        $urls = [];
        foreach($rooms as $room) {
            $urls[] = $this->generateUrl('room_show', ['id' => $room->getId()]);
        }

        return $this->render('annonces/index.html.twig', [
            'rooms' => $rooms,
            'urls' => $urls
        ]);
    }

    /**
     * @Route("/rooms/{id}", name="room_show", methods="GET")
     */
    public function showAction(Room $room): Response
    {
        return $this->render('annonces/show.html.twig', ['room' => $room]);
    } 


}
