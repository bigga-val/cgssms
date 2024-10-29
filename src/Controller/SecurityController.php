<?php

namespace App\Controller;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path:'/profile', name: 'app_profile')]
    public function profile(): Response
    {

        return $this->render('security/profile.html.twig');
    }

    #[Route(path:'/edit_profile', name: 'edit_profile')]
    public function edit_profile(Request $request, UserRepository $user, EntityManagerInterface $entityManager): Response
    {
        if($request->isMethod('POST')) {
//            dd($request->request->all());
            //dd($request->get('profile_token'));
            $myUser = $this->getUser();
            $myUser->setUsername($request->get('username'));
            $myUser->setEmail($request->get('email'));
            $myUser->setTelephone($request->get('telephone'));
            $entityManager->persist($myUser);
            $entityManager->flush();
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('security/edit.html.twig');
    }


}
