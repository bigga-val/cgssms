<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Form\OrganisationType;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/organisation')]
final class OrganisationController extends AbstractController
{
    #[Route(name: 'app_organisation_index', methods: ['GET'])]
    public function index(OrganisationRepository $organisationRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if($this->isGranted("ROLE_ADMIN")){
            $organisations = $organisationRepository->findAll();

        }else{

            $organisations = $organisationRepository->findBy(['user'=>$this->getUser()], ['designation' => 'ASC']);
        }

        return $this->render('organisation/index.html.twig', [
            'organisations' => $organisations
        ]);
    }

    #[Route('/new', name: 'app_organisation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $organisation = new Organisation();
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organisation->setUser($this->getUser());

            $entityManager->persist($organisation);
            $entityManager->flush();

            return $this->redirectToRoute('app_organisation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('organisation/new.html.twig', [
            'organisation' => $organisation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_organisation_show', methods: ['GET'])]
    public function show(Organisation $organisation): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('organisation/show.html.twig', [
            'organisation' => $organisation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_organisation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_organisation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('organisation/edit.html.twig', [
            'organisation' => $organisation,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/approve', name: 'app_organisation_approve', methods: ['GET', 'POST'])]
    public function approve(Request $request, Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        //dd($organisation);
        if($this->isGranted("ROLE_ADMIN")){
            $organisation->setApproved(true);
            $entityManager->persist($organisation);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_organisation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/disapprove', name: 'app_organisation_disapprove', methods: ['GET', 'POST'])]
    public function disapprove(Request $request, Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        //dd($organisation);
        if($this->isGranted("ROLE_ADMIN")){
            $organisation->setApproved(false);
            $entityManager->persist($organisation);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_organisation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_organisation_delete', methods: ['POST'])]
    public function delete(Request $request, Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$organisation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($organisation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_organisation_index', [], Response::HTTP_SEE_OTHER);
    }
}
