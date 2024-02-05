<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientFormType;
use App\Repository\ClientRepository;
use App\Services\RoleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ClientController extends AbstractController
{
    protected $roleService;
    protected $em;

    public function __construct(RoleService $roleService, ClientRepository $clientRepository)
    {
        $this->roleService = $roleService;
        $this->em = $clientRepository;
    }

    #[Route('/app/client', name: 'client')]
    public function index(): Response
    {
        $clients = $this->em->findBy(['status' => 'ACTIVE']);

        $actions = [
            [
                "title" => "Modifier",
                "link" => 'client_update'
            ],
            [
                "title" => "Supprimer",
                "link" => 'client_delete'
            ]
        ];

        $data = [];

        $clientResult = $this->em->findColumnNameDatabase();
        $clientsColumn = $clientResult['data'];

        foreach ($clientsColumn as $key => $client){
            if(preg_match('/^[a-z]+_[a-z]+$/i', $client['COLUMN_NAME'])){
                $firstName = strstr($client['COLUMN_NAME'], '_', true);
                $secondName = strstr($client['COLUMN_NAME'], '_');
                $reelName = substr($secondName, 1);
                $fullName = strtolower($firstName) . ucfirst($reelName);
                array_push($data, $fullName);
            } else {
                array_push($data, $client['COLUMN_NAME']);
            }
            if($key == count($clientsColumn) - 1){
                array_push($data, "action");
            }
        }

        $urlAdd = $this->generateUrl('client_update', 
        array(
            'id' => 'new-client'
        ), 
        UrlGeneratorInterface::ABSOLUTE_URL);

        $ajaxLink = "client_list_ajax";

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'columns' => $data,
            'ajaxLink' => $ajaxLink,
            'addButton' => $urlAdd,
            'actions' => $actions
        ]);
    }

    #[Route('/app/{id}/delete', name: 'client_delete')]
    public function delete(EntityManagerInterface $entityManagerInterface, $id){

        $client = $this->em
            ->find($id);

        if ( !$client ) {
            $this->addFlash('danger', 'Ce client est introuvable');
            return $this->redirectToRoute('client');
        }

        // $date = new \DateTime();
        //For RGPD
        // $client->setUsername( $client->getEmail() . '_' . $date->getTimestamp() );
        // $client->setEmail( $client->getEmail() . '_' . $date->getTimestamp() );
        /*
        $user->setEmailCanonical( $user->getEmail() . '_' . $date->getTimestamp() );
        $user->setUsername( $user->getEmail() . '_' . $date->getTimestamp() );
        $user->setUsernameCanonical( $user->getEmail() . '_' . $date->getTimestamp() );
        */

        $client->setStatus('DELETED');
        $client->updatedTimestamps();

        $entityManagerInterface->persist($client);
        $entityManagerInterface->flush();

        $this->addFlash('danger', 'Ce client a été supprimé');

        return $this->redirectToRoute('client');
    }

    #[Route('/app/{id}/update', name: 'client_update')]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, $id)
    {
        $userCurrent = $this->getUser();

        if( $id == 'new-client'){
            $client = new Client;
            $typeForm = "Ajouter";
        } else {
            $client = $this->em
            ->find($id);

            if ( !$client ) {
                $this->addFlash('danger', 'Ce client est introuvable');
                return $this->redirectToRoute('client');
            }

            $nameTemp = $client->getEmail();
            $typeForm = "Modifier";
        }

        if( $typeForm == 'Ajouter'){
            $form = $this->createForm(ClientFormType::class, $client, Array("validation_groups" => "update"));
        } else {
            $form = $this->createForm(ClientFormType::class, $client, Array("validation_groups" => "update"));
        }

        $formValid = null;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formValid = true;

            if($typeForm == 'Ajouter'){
                $emailExist = $this->em
                ->findBy(['email' => $client->getEmail()]);

                $exist = false;
                if($emailExist){
                    foreach($emailExist as $email){
                        if($email->getStatus() == "ACTIVE"){
                            $exist = true;
                        }
                    }

                    if($exist == true){
                        $this->addFlash('danger', 'Ce client a déjà été pris');
                        $formValid = false;
                    }
                }

                $client->setStatus('ACTIVE');
            } else {

                if($nameTemp != $client->getEmail()){
                    $emailExist = $this->em
                        ->findBy(['email' => $client->getEmail()]);

                    $exist = false;

                    if ( $emailExist ) {
                        foreach ($emailExist as $email) {
                            if($email->getStatus() == "ACTIVE"){
                                $exist = true;
                            }
                        }

                        if($exist == true){
                            $this->addFlash('danger', 'Ce client a déjà été pris');
                            $formValid = false;
                        }
                    }
                }
            }

            if($formValid){

                if ( $typeForm == 'Ajouter' ){
                    $client->setCreatedBy($userCurrent);
                    $client->setCreatedAt(new \DateTime('now'));
                }

                $client->updatedTimestamps();

                $entityManagerInterface->persist($client);
                $entityManagerInterface->flush();

                if($typeForm == 'Ajouter'){
                    $this->addFlash('success', 'Le client a été ajouté');
                } else {
                    $this->addFlash('success', 'Le client a été modifié');
                }

                return $this->redirectToRoute('client');
            }
        }

        return $this->render('client/update.html.twig', Array(
            "client" => $client,
            "clientId" => $id,
            "form" => $form->createView(),
            "typeForm" => $typeForm
        ));
    }

    #[Route('/app/client-ajax', name: 'client_list_ajax')]
    public function listAjax(Request $request): Response
    {
        //Load dataTable request
        $limitStart = $request->get("start");
        $limitWidth = $request->get("length");
        $limitSearch = $request->get("search");
        $limitOrder = $request->get("order");
        
        $data['data'] = [];

        $clientResult = $this->em->findAllAjax($limitStart, $limitWidth, $limitOrder, $limitSearch);
    
        $nbClientTotal = $clientResult['length'];
        $clients = $clientResult['data'];

        foreach ($clients as $client){

            $urlUpdate = $this->generateUrl('client_update', 
                array(
                    'id' => $client['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $urlDelete = $this->generateUrl('client_delete', 
                array(
                    'id' => $client['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $action = "<div class='edit-btn'>
            <button class='btn btn-primary btn-sm dropdown' type='button' id='dropdownMenuButtonAjaxDatatable' data-bs-toggle='dropdown' aria-expanded='false'><i class='ri-more-fill align-middle'></i></button>            
            <div class='dropdown-menu' aria-labelledby='dropdownMenuButtonAjaxDatatable'>
                <a class='dropdown-item' href='".$urlUpdate."'>Modifier</a>
                <a class='dropdown-item delete' href='".$urlDelete."' style='color: red; text-decoration: underline !important;'>Supprimer</a>
            </div>
            </div>";


            $data['data'][] = Array(
                "id" => $client['id'],
                "firstName" => $client['first_name'],
                "lastName" => $client['last_name'],
                "email" => $client['email'],
                "enterprise" => $client['enterprise'],
                "phone" => $client['phone'],
                "country" => $client['country'],
                "job" => $client['job'],
                "action" => $action,
            );
        }
        
        $data["draw"] = intval($request->get("draw")); 
        $data["recordsTotal"] = $nbClientTotal; 
        $data["recordsFiltered"] = $nbClientTotal;

        return new JsonResponse($data); 
    }

    #[Route('/app/client-gridjs', name: 'client_list_gridjs')]
    public function getDataForGridjs(): JsonResponse
    {
        $clients = $this->em->findAll();

        $data = [];
        foreach($clients as $client){
            $data[] = [
                'id' => $client->getId(),
                'firstName' => $client->getFirstName(),
                'lastName' => $client->getLastName(),
                'email' => $client->getEmail(),
                'enterprise' => $client->getEnterprise(),
                'phone' => $client->getPhone(),
                'country' => $client->getCountry(),
                'job' => $client->getJob(),
            ];
        }

        return $this->json([
            'data' => $data,
        ]);
    }
}



