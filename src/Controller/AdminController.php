<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Reservation;
use App\Entity\Commentaire;
use App\Entity\Owner;
use App\Entity\User;
use App\Repository\RoomRepository;
use App\Repository\OwnerRepository;
use App\Repository\ReservationRepository;
use App\Repository\CommentaireRepository;
use App\Repository\UserRepository;
use App\Form\UserType;
use App\Form\OwnerType;
use App\Form\RoomType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
     * @Route("/rooms/", name="admin_rooms")
     */
    public function rooms(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findAll();

        return $this->render('admin/rooms.html.twig', ['rooms' => $rooms]);
    }

    /**
     * @Route("/rooms/delete/{id}/", name="admin_room_delete")
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
     * @Route("/rooms/edit/{id}/", name="admin_room_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $room->setImageFile($form->get('imageFile')->getData());
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('message', 'La chambre a bien été éditée.');
            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/_edit.html.twig', ['room' => $room, 'form' => $form->createView()]);
    }

    /**
     * @Route("/reservations/", name="admin_reservations")
     */
    public function reservations(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy(['confirmed' => false]);

        return $this->render('admin/reservations.html.twig', ['reservations' => $reservations]);
    }

    /**
     * @Route("/reservations/validate/{id}/", name="admin_reservation_validate")
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
     * @Route("/reservations/cancel/{id}/", name="admin_reservation_cancel")
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
     * @Route("/moderation/", name="admin_moderation")
     */
    public function moderation(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findBy(['status' => 'EN ATTENTE DE MODERATION']);

        return $this->render('admin/moderation.html.twig', ['commentaires' => $commentaires]);
    }

    /**
     * @Route("/moderation/confirm/{id}/", name="admin_moderation_confirm")
     */
    public function confirm(Commentaire $commentaire): Response
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire->setStatus('VALIDE');
        $em->flush();

        $this->get('session')->getFlashBag()->add('message', 'Le commentaire a bien été validé.');
        return $this->redirectToRoute('admin_moderation');
    }

    /**
     * @Route("/moderation/delete/{id}/", name="admin_moderation_delete")
     */
    public function deleteCom(Commentaire $commentaire): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($commentaire);
        $em->flush();

        $this->get('session')->getFlashBag()->add('message', 'Le commentaire a bien été supprimé.');
        return $this->redirectToRoute('admin_moderation');
    }

    /**
     * @Route("/owners/", name="admin_owners")
     */
    public function showOwners(OwnerRepository $ownerRepository): Response
    {
        $owners = $ownerRepository->findAll();

        return $this->render('admin/owners.html.twig', ['owners' => $owners]);
    }

    /**
     * @Route("/owners/delete/{id}/", name="admin_owner_delete")
     */
    public function ownerDelete(Owner $owner): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($owner);
        $em->flush();

        $this->get('session')->getFlashBag()->add('message', 'Le propriétaire a bien été supprimé.');
        return $this->redirectToRoute('admin_owners');
    }

    /**
     * @Route("/superadmin/", name="superadmin", methods={"GET", "POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function superadmin(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));
            $user->addRole('ROLE_ADMIN');
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('message', 'Compte crée avec succès.');
            return $this->redirectToRoute('superadmin', [], Response::HTTP_SEE_OTHER);
        }

        $admins = $userRepository->findAdmins();
        return $this->render('admin/superadmin.html.twig', ['admins' => $admins, 'form' => $form->createView()]);
    }

    /**
     * @Route("/superadmin/edit/{id}/", name="superadmin_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function superadminEdit(Request $request, User $admin, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admin->setPassword($passwordEncoder->encodePassword($admin, $form->get('password')->getData()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($admin);
            $em->flush();

            $this->get('session')->getFlashBag()->add('message', 'Le compte collaborateur a bien été édité');
            return $this->redirectToRoute('superadmin');
        }

        return $this->render('admin/_edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/superadmin/delete/{id}/", name="superadmin_delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function superadminDelete(Request $request, User $admin): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($admin);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Le compte collaborateur a bien été supprimé.');
        return $this->redirectToRoute('superadmin');
    }
}
