<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Entity\Region;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('index/rooms.html.twig', [ 'rooms' => $rooms ]);
    }

    /**
     * @Route("/rooms/{id}", name="home_room_show", methods={"GET"})
     */
    public function show(Room $room): Response
    {
        return $this->render('index/show.html.twig', ['room' => $room]);
    }

    /**
     * @Route("/reservation/{id}", name="home_reservation", methods={"GET", "POST"})
     */
    public function reserve(Room $room): Response
    {
        return $this->render('index/show.html.twig', ['room' => $room]);
    }
}
