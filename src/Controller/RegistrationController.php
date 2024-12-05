<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
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
                                UserRepository $userRepository
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
            $user->setTotalSMS(5);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
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
}


