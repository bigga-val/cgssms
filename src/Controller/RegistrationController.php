<?php

namespace App\Controller;

use App\Entity\PwdResetting;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\PwdResettingRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
//use Symfony\Component\Validator\Constraints\DateTime;
use DateTime;
use DateTimeZone;

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

    #[Route('/JsonCheckExistentUser', name: 'JsonCheckExistentUser', methods: ['GET'])]
    public function JsonCheckExistentUser(UserRepository $userRepository,
                                          Request $request,
                                            EntityManagerInterface $entityManager,
                                            EmailService $emailService
    ): JsonResponse
    {
        $email = $request->get('email');
        $username = $request->get('username');
        $user = $userRepository->findBy(['Email' => $email, 'username' => $username]);

        if (count($user) === 0) {
            return new JsonResponse("Aucun utilisateur éxistant.");
        } else if (count($user) > 1) {
            return new JsonResponse("Un problème est survenu. Veuillez contacter votre administrateur.");
        }else{
            $pwdreset = new PwdResetting();
            $pwdreset->setUser($user[0]);
            $pwdreset->setActive(true);
            $pwdreset->setDatecreation(new \DateTime());
            $entityManager->persist($pwdreset);
            $entityManager->flush();

            //send email when the account is created
            $subject = "Réinitialisation de votre mot de passe";
            $body = $emailService->pwdResetBody($user[0]->getUsername(), $pwdreset->getId());
            $emailService->sendEmail($user[0]->getEmail(),$subject, $body);
//            $emailService->sendEmail("gabrielkatonge@gmail.com",$subject, $body);

            return  new JsonResponse(['data'=>true, 'pwdid'=>$pwdreset->getId()]);

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

    #[Route('/reset', name: 'app_reset')]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                                  EntityManagerInterface $entityManager,
                                  UserRepository $userRepository,
                                  EmailService $emailService,
                                    PwdResettingRepository $pwdResettingRepository
    ): Response
    {

        return $this->render('registration/reset.html.twig', []);
    }

    #[Route('/resetter', name: 'app_resetter')]
    public function resetter(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                          EntityManagerInterface $entityManager,
                          UserRepository $userRepository,
                          EmailService $emailService,
                            PwdResettingRepository $pwdResettingRepository
    ): Response
    {
        $pwdid = $request->get('id');
        if($pwdid == null){
            return $this->redirectToRoute('app_login');
        }
        $reset = $pwdResettingRepository->find($pwdid);
        if(!$reset->isActive()){
            return $this->redirectToRoute('app_expired_link');
        }
        //$date_donnee_str = $reset->getDatecreation(); // Date donnée au format YYYY-MM-DD HH:MM:SS
        $date_donnee = $reset->getDatecreation(); //new DateTime($date_donnee_str);

        $date_actuelle = new DateTime(); // Date actuelle avec le fuseau horaire par défaut du serveur

// Important : Définir le même fuseau horaire pour les deux dates pour une comparaison correcte
        $timezone = new DateTimeZone('Europe/Paris'); // Exemple : fuseau horaire de Paris. Adaptez-le à votre besoin.

        $date_donnee->setTimezone($timezone);
        $date_actuelle->setTimezone($timezone);

        $diff = $date_actuelle->diff($date_donnee);
        //$diff = $date_actuelle - $date_donnee;
        if ($diff->h >= 1){// && $diff->i === 0 && $diff->s === 0 && $diff->days === 0) {
            return $this->redirectToRoute('app_expired_link');
        }
            return $this->render('registration/resetter.html.twig', [
            "pwdid" => $pwdid,
//                "date_actuelle" => $date_actuelle,
//                "date_donnee" => $date_donnee,
//                "result"=>$diff->h >= 1
        ]);
    }

    #[Route('/JsonSaveResetPwd', name: 'app_JsonSaveResetPwd')]
    public function JsonSaveResetPwd(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             UserRepository $userRepository,
                             EmailService $emailService,
                                PwdResettingRepository $pwdResettingRepository
    ): Response
    {
        $pwd= $request->get('pwd');
        $pwd2= $request->get('pwd2');
        $pwdid = $request->get('pwdid');
        $reset = $pwdResettingRepository->find($pwdid);
        $user = $userRepository->find($reset->getUser());
        $user->setPassword($userPasswordHasher->hashPassword($user, $pwd));
        $entityManager->persist($user);
        $reset->setActive(false);
        $entityManager->flush();

        return new JsonResponse(['data'=>true, 'msg'=>"Votre mot de passe a bien été réinitialisé."]);
    }
}


