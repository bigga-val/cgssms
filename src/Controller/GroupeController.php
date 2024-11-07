<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\Organisation;
use App\Form\GroupeType;
use App\Repository\ContactRepository;
use App\Repository\GroupeRepository;
use App\Repository\OrganisationRepository;
use Couchbase\RegexpSearchQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

#[Route('/groupe')]
final class GroupeController extends AbstractController
{
    #[Route(name: 'app_groupe_index', methods: ['GET'])]
    public function index(GroupeRepository $groupeRepository): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
            $groupes = $groupeRepository->findGroupes();

        }else{
            $groupes = $groupeRepository->findGroupesByUser($this->getUser());

        }
        //dd($groupes);
        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupes,
        ]);
    }

    #[Route('/new', name: 'app_groupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,
        OrganisationRepository $organisationRepository
    ): Response
    {
        $groupe = new Groupe();
        $form = $this->createForm(GroupeType::class, $groupe);
        $organisations = $organisationRepository->findBy(['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupe->setActive(true);
            $entityManager->persist($groupe);
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe/new.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
            'organisations' => $organisations,
        ]);
    }

    #[Route('/JsonSaveGroupe', name: 'json_groupe_new', methods: ['GET', 'POST'])]
    public function JsonSaveGroupe(Request $request, EntityManagerInterface $entityManager,
        OrganisationRepository $organisationRepository
    ): JsonResponse
    {
        $groupe = new Groupe();
        $groupe->setActive(true);
        $groupe->setDesignation($request->get('designation'));
        $groupe->setOrganisation($organisationRepository->find($request->get('organisationID')));
        $entityManager->persist($groupe);
        $entityManager->flush();

//            $entityManager->flush();
//        if ($form->isSubmitted() && $form->isValid()) {
//            $groupe->setActive(true);
//            $entityManager->persist($groupe);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
//        }

        return new JsonResponse($groupe->getId());
    }

    #[Route('/{id}', name: 'app_groupe_show', methods: ['GET'])]
    public function show(Groupe $groupe, ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findBy(['groupe' => $groupe]);
        return $this->render('groupe/show.html.twig', [
            'groupe' => $groupe,
            'contacts' => $contacts,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe/edit.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_delete', methods: ['POST'])]
    public function delete(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupe->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($groupe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/JsonListGroupsByOrganisation/{id}', name: 'JsonListGroupsByOrganisation', methods: ['GET'])]
    public function JsonListGroupsByOrganisation(Request $request, Organisation $organisation): JsonResponse
    {

        return new JsonResponse(['data'=>$organisation]);
    }
}
