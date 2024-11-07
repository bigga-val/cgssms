<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Groupe;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Repository\GroupeRepository;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact')]
final class ContactController extends AbstractController
{
    #[Route(name: 'app_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
            $contacts = $contactRepository->findAll();

        }else{

            $contacts = $contactRepository->findContactsByUser($this->getUser()->getId());
        }
        //dd($contacts);
        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/JsonSaveContact', name: 'json_contact_new', methods: ['GET', 'POST'])]
    public function JsonSaveGroupe(Request $request, EntityManagerInterface $entityManager,
                                   GroupeRepository $groupeRepository
    ): JsonResponse
    {
        $contact = new Contact();
        $contact->setTelephone($request->get('telephone'));
        $contact->setNom($request->get('nom'));
        $contact->setPostnom($request->get('postnom'));
        $contact->setAdresse($request->get('adresse'));
        $contact->setFonction($request->get('fonction'));
        $contact->setgroupe($groupeRepository->find($request->get('groupeID')));
        $entityManager->persist($contact);
        $entityManager->flush();


        return new JsonResponse(true);
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
