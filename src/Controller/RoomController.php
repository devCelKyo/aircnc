<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Commentaire;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use App\Repository\RegionRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/owner")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/", name="room_index", methods={"GET"})
     */
    public function index(RoomRepository $roomRepository): Response
    {
        // Si l'utilisateur est un administrateur, on montre toutes les rooms
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $this->get('session')->getFlashBag()->add('message', 'Vous êtes connecté en tant qu\'administrateur, vous voyez donc tous les Couette et Cafés');
            $rooms = $roomRepository->findAll();
        } // Sinon ça veut dire que c'est un propriétaire, et on ne montre que les rooms qui sont à lui (Simple bon sens, mais on dira RGPD pour avoir l'air cool)
        else
        {
            $rooms = $roomRepository->findBy(['owner' => $this->getUser()->getOwner()]);
        }

        return $this->render('room/index.html.twig', ['rooms' => $rooms]);
    }

    /**
     * @Route("/new", name="room_new", methods={"GET","POST"})
     */
    public function new(Request $request, RegionRepository $regionRepository): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $regions = $form->get('regions')->getData();
            $room->addRegion($regions);
            $room->setImageFile($form->get('imageFile')->getData());

            $room->setOwner($this->getUser()->getOwner());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('message', $regions);
            return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_show", methods={"GET"})
     */
    public function show(Room $room, CommentaireRepository $commentaireRepository): Response
    {
        if (!$this->getUser()->getOwner()->owns($room) && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'avez pas le droit d\'accéder à ceci');
            return $this->redirectToRoute('room_index');
        }

        $commentaires = $commentaireRepository->findBy(['status' => 'EN ATTENTE DE CONFIRMATION', 'room' => $room]);
        return $this->render('room/show.html.twig', [
            'room' => $room,
            'commentaires' => $commentaires,
            'owner' => $room->getOwner()
        ]);
    }

    /**
     * @Route("/commentaire/accept/{id}", name="room_commentaire_accept")
     */
    public function commentaireAccept(Commentaire $commentaire): Response
    {
        if (!$this->getUser()->getOwner()->owns($commentaire->getRoom())) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'avez pas le droit de faire ceci');
            return $this->redirectToRoute('room_index');
        }

        $commentaire->setStatus('VALIDE');
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('success', 'Commentaire validé.');
        return $this->redirectToRoute('room_show', ['id' => $commentaire->getRoom()->getId()]);
    }

    /**
     * @Route("/commentaire/report/{id}", name="room_commentaire_report")
     */
    public function commentaireReport(Commentaire $commentaire): Response
    {
        if (!$this->getUser()->getOwner()->owns($commentaire->getRoom())) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'avez pas le droit de faire ceci');
            return $this->redirectToRoute('room_index');
        }

        $commentaire->setStatus('EN ATTENTE DE MODERATION');
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('message', 'Le commentaire a bien été signalé et sera traité sous peu.');
        return $this->redirectToRoute('room_show', ['id' => $commentaire->getRoom()->getId()]);
    }
    /**
     * @Route("/{id}/edit", name="room_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Room $room): Response
    {
        if (!$this->getUser()->getOwner()->owns($room) && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'avez pas le droit d\'accéder à ceci');
            return $this->redirectToRoute('room_index');
        }

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_delete", methods={"POST"})
     */
    public function delete(Request $request, Room $room): Response
    {
        if (!$this->getUser()->getOwner()->owns($room) && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'avez pas le droit d\'accéder à ceci');
            return $this->redirectToRoute('room_index');
        }
        
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
    }
}
