<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Repository\ContactRepository;
use App\Repository\GroupeRepository;
use App\Repository\HistoriqueRepository;
use App\Repository\OrganisationRepository;
use App\Repository\TemplatesmsRepository;
use App\Entity\Templatesms;
use App\Repository\UserRepository;
use App\Service\EmailService;
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
    #[Route('/dashboard', name: 'app_home')]
    public function index(
        ContactRepository $contactRepository,
        GroupeRepository  $groupeRepository,
        TemplatesmsRepository $templateRepository,
        HistoriqueRepository $historiqueRepository,
        OrganisationRepository $organisationRepository,
    ): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if ($this->isGranted('ROLE_ADMIN')) {
            $historiques = $historiqueRepository->findBy([], ['date' => 'DESC'], 5);
            $contacts = $contactRepository->findContacts(true);
            $groupes = $groupeRepository->findGroupes();
            $organisations = $organisationRepository->findAll();

        }else{
            $historiques = $historiqueRepository->findBy(['user'=>$this->getUser()], ['date' => 'DESC'], 5);

            $contacts = $contactRepository->findContactsByUser($this->getUser()->getId());
            $groupes = $groupeRepository->findGroupesByUser($this->getUser()->getId());
            $organisations = $organisationRepository->findBy(["user"=>$this->getUser()]);

        }

        //dd($contacts, count($contacts), $groupes, count($groupes));

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'contacts' => count($contacts),
            'groupes' => count($groupes),
            'historiques' => $historiques,
            'organisations' => $organisations,
        ]);
    }

    #[Route('/', name: 'app_landing')]
    public function landing(): Response
    {
                //dd($contacts, count($contacts), $groupes, count($groupes));

        return $this->render('home/landing.html.twig', [
        ]);
    }

    #[Route('/commander', name: 'app_commande')]
    public function commande(EmailService $emailService): Response
    {
        //dd($contacts, count($contacts), $groupes, count($groupes));
        return $this->render('home/commande.html.twig', [

            ]);
    }

    #[Route('/confirmer', name: 'app_confirmer')]
    public function confirmer(Request $request): Response
    {
        //dd($contacts, count($contacts), $groupes, count($groupes));
        $prix = $request->get('prix');
        $montant = $request->get('montant');
        $total = intval($montant) / floatval($prix);
        return $this->render('home/confirmer.html.twig', [
            "prix"=>$prix,
            "montant"=>$montant,
            "total"=>intval($total),
            "email"=>$this
            ]);
    }


    #[Route('/envoi', name: 'app_envoi_sms')]
    public function envoi(Request $request,
                          OrganisationRepository $organisationRepository,
                            GroupeRepository $groupeRepository,
                            TemplatesmsRepository $templatesmsRepository,

    ): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
            $organisations = $organisationRepository->findAll();
            $templates = $templatesmsRepository->findAll();
        }else{
            $organisations = $organisationRepository->findBy(["user"=>$this->getUser()]);
            $templates = $templatesmsRepository->findBy(["user"=>$this->getUser()]);
        }


        return $this->render("home/envoi.html.twig", [
            'organisations' => $organisations,
            'templates' => $templates,
        ]);
    }

    #[Route('/envoyer', name: 'app_envoyer_sms')]
    public function envoyer($numero, $message, $sender)
    {
            try {
                //$destinataire = "";
                //$message = "";
                //$sender = "";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api-2.mtarget.fr/messages?username=vousdabord&password=XJK8H8sDP4X0QoWe0OGebAbzRkgwbq1O&msisdn=" . $numero . "&msg=" . $message . "&sender=" . $sender,
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
                return $response ;//. ' || ' . $err;

            } catch (Exception $e) {
                return $e->getMessage();
            }

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

    #[Route('/EnvoiRapideSMS', name: 'EnvoiRapideSMS', methods: ['GET'])]
    public function EnvoiRapideSMS(Request $request,
                                   EntityManagerInterface $entityManager,
                                   ContactRepository $contactRepository,
                                   HistoriqueController $historiqueController,
                                    UserRepository $userRepository,
                                    OrganisationRepository $organisationRepository,
    ): Response
    {
        $message = $request->get("message");
        //$sender = 'mulykap';//$request->get("sender");
        $sender = $request->get("expediteur");
        if($sender != null && $sender != -1 && $organisationRepository->find($sender)->isApproved()){
            $sender = $organisationRepository->find($sender)->getDesignation();
        }else{
            $sender = 'insoft';
        }
        //dd($sender);
        $numero = $request->get("numero");

        $message = str_replace(' ', '+', $message);
        $numero = '%2b243'.substr($numero, -9);
        $response = $this->envoyer($numero, $message, $sender);
        $data = json_decode($response, true);

        // Accéder aux valeurs souhaitées
        $code = $data['results'][0]['code'];
        $reason = $data['results'][0]['reason'];
//                    $ticket = $data['results'][0]['ticket'];

        $historiqueController->create($sender, $message, $numero, $code, $reason,
            $entityManager
        );
        if($code == 0){
            $user = $userRepository->find($this->getUser()->getId());
            $user->setTotalSMS($user->getTotalSMS() - 1);
            $user->setUsedSMS($user->getUsedSMS() + 1);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        //dd($response);
        return $this->redirectToRoute('app_home');

    }

    #[Route('/JsonEnvoyerSMS', name: 'JsonEnvoyerSMS', methods: ['GET'])]
    public function JsonEnvoyerSMS(Request $request,
                                     EntityManagerInterface $entityManager,
                                    ContactRepository $contactRepository,
                                    HistoriqueController $historiqueController,
                                    OrganisationRepository $organisationRepository,
    UserRepository $userRepository,
    ): JsonResponse
    {
        $message = $request->get("message");
        $organisation = $organisationRepository->find($request->get("expID"));
        $sender = 'insoft';
        if($organisation->isApproved()){
            $sender = $organisation->getDesignation();
        }
        //$sender = 'mulykap';//$request->get("sender");
        $groupeID = $request->get("groupeID");
        $contacts = $contactRepository->findBy(['groupe'=>$groupeID]);

        $user = $userRepository->find($this->getUser()->getId());

        $response = '';
        if(count($contacts)==0){
            return new JsonResponse("Aucun contact trouve");
        }else if(count($contacts) >= $user->getTotalSMS()) {
            return new JsonResponse("Vous n'avez pas assez de credit.");
        }else{
            $numbers = "";
            foreach ($contacts as $contact) {
                try {
                $tosend = $message;
                if (str_contains($tosend, '[Nom]')) {
                    $tosend = str_replace('[Nom]', $contact->getNom(), $tosend);
                }
                if (str_contains($tosend, '[Postnom]')) {
                    $tosend = str_replace('[Postnom]', $contact->getPostnom(), $tosend);
                }
                if (str_contains($tosend, '[Adresse]')) {
                    $tosend = str_replace('[Adresse]', $contact->getAdresse(), $tosend);
                }
                if (str_contains($tosend, '[Fonction]')) {
                    $tosend = str_replace('[Fonction]', $contact->getFonction(), $tosend);
                }
                $tosend = str_replace(' ', '+', $tosend);
                $numero = '%2b243'.substr($contact->getTelephone(), -9);
                $response = $this->envoyer($numero, $tosend, $sender);
                $data = json_decode($response, true);

                    // Accéder aux valeurs souhaitées
                    $code = $data['results'][0]['code'];
                    $reason = $data['results'][0]['reason'];
//                    $ticket = $data['results'][0]['ticket'];

                    $historiqueController->create($sender, $tosend, $numero, $code, $reason,
                    $entityManager);
                    if($code == 0){
                        $user->setTotalSMS($user->getTotalSMS() - 1);
                        $user->setUsedSMS($user->getUsedSMS() + 1);
                        $entityManager->persist($user);
                        $entityManager->flush();
                    }
                }catch (\Exception $e){
                    return new JsonResponse([false, $e->getMessage()]);
                }
            }
            return new JsonResponse('true');
        }
    }
}
