<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                                UserRepository $userRepository,
                             EmailService $emailService
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */

            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setEmail($user->getEmail());
            $user->setUsedSMS(0);
            $user->setTotalSMS(0);
            $user->setDateCreation(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            //send email when the account is created
            $subject = "Confirmez votre inscription et profitez pleinement de Rapide SMS";
            $body = $emailService->confirmerCompteBody($user->getUsername(), $user->getEmail(), $user->getId());
            $emailService->sendEmail($user->getEmail(),$subject, $body);

            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_confirm');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/JsonCheckCredentials', name: 'JsonCheckCredentials', methods: ['GET'])]
    public function JsonCheckCredentials(UserRepository $userRepository, Request $request): JsonResponse
    {
        $email = $request->get('email');
        $username = $request->get('username');
        $emails = $userRepository->findBy(['Email' => $email]);
        $usernames = $userRepository->findBy(['username' => $username]);
        if (count($emails) > 0) {
            return new JsonResponse("Email déjà existant.");
        } else if (count($usernames) > 0) {
            return new JsonResponse("Nom d'utilisateur déjà existant.");
        }else{
            return  new JsonResponse(true);
        }
    }

    #[Route('/confirm', name: 'app_confirm')]
    public function confirmcompte(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             UserRepository $userRepository,
                             EmailService $emailService
    ): Response
    {
        return $this->render('registration/confirm.html.twig', []);
    }

        #[Route('/confirmer', name: 'app_confirmer')]
    public function confirmercompte(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                                    EntityManagerInterface $entityManager,
                                    UserRepository $userRepository,
                                    EmailService $emailService
    ): Response
    {
        $userID = $request->get('id');
        $user = $userRepository->find($userID);
        if($user->isConfirmer() == null){
            $user->setTotalSMS(5);
            $user->setConfirmer(true);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');

        }else{
            return $this->redirectToRoute('app_expired_link');
        }

    }

    #[Route('/expired', name: 'app_expired_link')]
    public function expired_link(Request $request, UserPasswordHasherInterface $userPasswordHasher,

    ): Response
    {
        return $this->render('registration/expired_link.html.twig', []);
    }
}


