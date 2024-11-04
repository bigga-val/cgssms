<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Repository\ContactRepository;
use App\Repository\GroupeRepository;
use App\Repository\OrganisationRepository;
use App\Repository\TemplatesmsRepository;
use App\Entity\Templatesms;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Constraint\Count;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ContactRepository $contactRepository,
        GroupeRepository  $groupeRepository, TemplatesmsRepository $templateRepository,
    ): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $contacts = $contactRepository->findContactsByUser($this->getUser()->getId());
        $groupes = $groupeRepository->findGroupesByUser($this->getUser()->getId());
        //dd($contacts, count($contacts), $groupes, count($groupes));

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'contacts' => count($contacts),
            'groupes' => count($groupes),
        ]);
    }

    #[Route('/envoi', name: 'app_envoi_sms')]
    public function envoi(Request $request,
                          OrganisationRepository $organisationRepository,
                            GroupeRepository $groupeRepository,
                            TemplatesmsRepository $templatesmsRepository,

    ): Response
    {
        $organisations = $organisationRepository->findBy(["user"=>$this->getUser()]);
        $groupes = $groupeRepository->findBy(["organisation"=>1]);
        $templates = $templatesmsRepository->findBy(["user"=>$this->getUser()]);

        return $this->render("home/envoi.html.twig", [
            'organisations' => $organisations,
            'templates' => $templates,
        ]);
    }

    #[Route('/envoyer', name: 'app_envoyer_sms')]
    public function envoyer($numero, $message, $sender)
    {
            try {
                $destinataire = "";
                $message = "";
                $sender = "";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api-2.mtarget.fr/messages?username=vousdabord&password=XJK8H8sDP4X0QoWe0OGebAbzRkgwbq1O&msisdn=" . $destinataire . "&msg=" . $message . "&sender=" . $sender,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    //CURLOPT_POSTFIELDS => "username=vousdabord&password=XJK8H8sDP4X0QoWe0OGebAbzRkgwbq1O&msisdn=+243995053623&msg=Message%20simple",
                    CURLOPT_HTTPHEADER => array(
                        "content-type: application/x-www-form-urlencoded"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                //dd("Erreur: ", $err, "Response:", $response);
                //return $this->redirectToRoute('app_home');
            } catch (Exception $e) {

            }
        return $response;

    }
    #[Route('/JsonListGroupsByOrganisation/{id}', name: 'JsonListGroupsByOrganisation', methods: ['GET'])]
    public function JsonListGroupsByOrganisation(Request $request,
                                                 OrganisationRepository $organisationRepository,
                                                 GroupeRepository $groupeRepository,
    ): JsonResponse
    {
        //$groupes = $groupeRepository->findBy(["organisation"=>$organisationRepository->find($request->get("id"))]);
        $groupes = $groupeRepository->findGroupesByOrganisation($request->get("id"));

        return new JsonResponse($groupes);
    }

    #[Route('/JsonSaveTemplate', name: 'JsonSaveTemplate', methods: ['GET'])]
    public function JsonSaveTemplate(Request $request,
                                     EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $templatesms = new Templatesms();
        $templatesms->setUser($this->getUser());
        $templatesms->setTexte($request->get("texte"));
        $templatesms->setTitre($request->get("titre"));
        $entityManager->persist($templatesms);
        $entityManager->flush();
        return new JsonResponse([$request->get("titre"), $request->get("texte")]);
    }

    #[Route('/JsonEnvoyerSMS', name: 'JsonEnvoyerSMS', methods: ['GET'])]
    public function JsonEnvoyerSMS(Request $request,
                                     EntityManagerInterface $entityManager,
                                    ContactRepository $contactRepository,

    ): JsonResponse
    {
        $message = $request->get("message");
        $sender = $request->get("sender");
        $groupeID = $request->get("groupeID");
        $contacts = $contactRepository->findBy(['groupe'=>$groupeID]);
        if(count($contacts)==0){
            return new JsonResponse("Aucun contact trouve");
        }else{
            foreach ($contacts as $contact) {
                $numero = '%2b'.substr($contact->getTelephone(), -9);
                $response = $this->envoyer($numero, $message, $sender);
            }
        }
        return new JsonResponse($response);
    }
}
