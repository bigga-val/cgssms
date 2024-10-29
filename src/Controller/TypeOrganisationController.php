<?php

namespace App\Controller;

use App\Entity\TypeOrganisation;
use App\Form\TypeOrganisationType;
use App\Repository\TypeOrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/type/organisation')]
final class TypeOrganisationController extends AbstractController
{
    #[Route(name: 'app_type_organisation_index', methods: ['GET'])]
    public function index(TypeOrganisationRepository $typeOrganisationRepository): Response
    {
        return $this->render('type_organisation/index.html.twig', [
            'type_organisations' => $typeOrganisationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_organisation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeOrganisation = new TypeOrganisation();
        $form = $this->createForm(TypeOrganisationType::class, $typeOrganisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeOrganisation->setActive(true);
            //$typeOrganisation->isActive(true);
            $entityManager->persist($typeOrganisation);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_organisation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_organisation/new.html.twig', [
            'type_organisation' => $typeOrganisation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_organisation_show', methods: ['GET'])]
    public function show(TypeOrganisation $typeOrganisation): Response
    {
        return $this->render('type_organisation/show.html.twig', [
            'type_organisation' => $typeOrganisation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_organisation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeOrganisation $typeOrganisation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeOrganisationType::class, $typeOrganisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_organisation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_organisation/edit.html.twig', [
            'type_organisation' => $typeOrganisation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_organisation_delete', methods: ['POST'])]
    public function delete(Request $request, TypeOrganisation $typeOrganisation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeOrganisation->getId(), $request->getPayload()->getString('_token'))) {
            //$entityManager->remove($typeOrganisation);
            $typeOrganisation->setActive(false);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_organisation_index', [], Response::HTTP_SEE_OTHER);
    }
}
