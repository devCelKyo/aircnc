<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Owner;
use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register", methods={"GET", "POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));

            // Création des objets owner et client liés à user
            $owner = new Owner();
            $owner->setFirstname($user->getFirstname());
            $owner->setLastname($user->getLastname());
            $owner->setAddress($form->get('address')->getData());
            $owner->setCountry($form->get('country')->getData());

            $client = new Client();
            $client->setFirstname($user->getFirstname());
            $client->setLastname($user->getLastname());
            $client->setAddress($form->get('address')->getData());
            $client->setCountry($form->get('country')->getData());

            $user->setOwner($owner);
            $user->setClient($client);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }
}
