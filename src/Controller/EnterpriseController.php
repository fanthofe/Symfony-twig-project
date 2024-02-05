<?php

namespace App\Controller;

use App\Entity\Enterprise;
use App\Form\EnterpriseFormType;
use App\Repository\EnterpriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EnterpriseController extends AbstractController
{
    protected $em;
    
    public function __construct(EnterpriseRepository $enterpriseRepository)
    {
        $this->em = $enterpriseRepository;
    }

    #[Route('/app/enterprise', name: 'enterprise')]
    public function index(): Response
    {
        $enterprises = $this->em->findby(['status' => 'ACTIVE']);

        $actions = [
            [
                "title" => "Modifier",
                "link" => 'enterprise_update'
            ],
            [
                "title" => "Supprimer",
                "link" => 'enterprise_delete'
            ]
        ];

        $data = [];
        $columnFr = [
            "ID",
            "Nom",
            "Domaine",
            "Adresse",
            "Ville",
            "Pays",
            "Téléphone",
            "Siret",
            "Date de création",
            "Nombre de directeurs",
            "Actions"
        ];

        $enterpriseResult = $this->em->findColumnNameDatabase();
        $enterprisesColumn = $enterpriseResult['data'];

        foreach ($enterprisesColumn as $key => $enterprise){
            if(preg_match('/^[a-z]+_[a-z]+$/i', $enterprise['COLUMN_NAME'])){
                $firstName = strstr($enterprise['COLUMN_NAME'], '_', true);
                $secondName = strstr($enterprise['COLUMN_NAME'], '_');
                $reelName = substr($secondName, 1);
                $fullName = strtolower($firstName) . ucfirst($reelName);
                array_push($data, $fullName);
            } else {
                array_push($data, $enterprise['COLUMN_NAME']);
            }
            if($key == count($enterprisesColumn) - 1){
                array_push($data, "action");
            }
        }

        $urlAdd = $this->generateUrl('enterprise_update', 
        array(
            'id' => 'new-enterprise'
        ), 
        UrlGeneratorInterface::ABSOLUTE_URL);

        $ajaxLink = "enterprise_list_ajax";

        return $this->render('enterprise/index.html.twig', [
            'enterprises' => $enterprises,
            'columns' => $data,
            "columnFr" => $columnFr,
            'ajaxLink' => $ajaxLink,
            'addButton' => $urlAdd,
            'actions' => $actions
        ]);
    }

    #[Route('/app/enterprise/{id}/delete', name: 'enterprise_delete')]
    public function delete(EntityManagerInterface $entityManagerInterface, $id)
    {
        $enterprise = $this->em
            ->find($id);

        if ( !$enterprise ) {
            $this->addFlash('danger', 'Cette entreprise est introuvable');
            return $this->redirectToRoute('enterprise');
        }

        $enterprise->setStatus('DELETED');
        $enterprise->updatedTimestamps();

        $entityManagerInterface->persist($enterprise);
        $entityManagerInterface->flush();

        $this->addFlash('danger', 'Cette entreprise a été supprimée');

        return $this->redirectToRoute('enterprise');
    }

    #[Route('/app/enterprise/{id}/update', name: 'enterprise_update')]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, $id)
    {
        $userCurrent = $this->getUser();

        if( $id == 'new-enterprise'){
            $enterprise = new Enterprise;
            $typeForm = "Ajouter";
        } else {
            $enterprise = $this->em
            ->find($id);

            if ( !$enterprise ) {
                $this->addFlash('danger', 'Cette entreprise est introuvable');
                return $this->redirectToRoute('enterprise');
            }

            $nameTemp = $enterprise->getName();
            $typeForm = "Modifier";
        }

        if( $typeForm == 'Ajouter'){
            $form = $this->createForm(EnterpriseFormType::class, $enterprise, Array("validation_groups" => "update"));
        } else {
            $form = $this->createForm(EnterpriseFormType::class, $enterprise, Array("validation_groups" => "update"));
        }

        $formValid = null;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formValid = true;

            if($typeForm == 'Ajouter'){
                $nameExist = $this->em
                ->findBy(['name' => $enterprise->getName()]);

                $exist = false;
                if($nameExist){
                    foreach($nameExist as $name){
                        if($name->getStatus() == "ACTIVE"){
                            $exist = true;
                        }
                    }

                    if($exist == true){
                        $this->addFlash('danger', 'Cette entreprise est déjà prise');
                        $formValid = false;
                    }
                }

                $enterprise->setStatus('ACTIVE');
            } else {

                if($nameTemp != $enterprise->getName()){
                    $nameExist = $this->em
                        ->findBy(['name' => $enterprise->getName()]);

                    $exist = false;

                    if ( $nameExist ) {
                        foreach ($nameExist as $name) {
                            if($name->getStatus() == "ACTIVE"){
                                $exist = true;
                            }
                        }

                        if($exist == true){
                            $this->addFlash('danger', 'Cette entreprise est déjà prise');
                            $formValid = false;
                        }
                    }
                }
            }

            if($formValid){

                if ( $typeForm == 'Ajouter' ){
                    $enterprise->setCreatedBy($userCurrent)
                    ->setCreatedAt(new \DateTime('now'));
                }

                $enterprise->updatedTimestamps();

                $entityManagerInterface->persist($enterprise);
                $entityManagerInterface->flush();

                if($typeForm == 'Ajouter'){
                    $this->addFlash('success', "L'entreprise a été ajoutée");
                } else {
                    $this->addFlash('success', "L'entreprise a été modifiée");
                }

                return $this->redirectToRoute('enterprise');
            }
        }
        
        return $this->render('enterprise/update.html.twig', Array(
            "enterprise" => $enterprise,
            "enterpriseId" => $id,
            "form" => $form->createView(),
            "typeForm" => $typeForm
        ));
    }

    #[Route('/app/enterprise-ajax', name: 'enterprise_list_ajax')]
    public function listAjax(Request $request): Response
    {
        //Load dataTable request
        $limitStart = $request->get("start");
        $limitWidth = $request->get("length");
        $limitSearch = $request->get("search");
        $limitOrder = $request->get("order");
        
        $data['data'] = [];

        $enterpriseResult = $this->em->findAllAjax($limitStart, $limitWidth, $limitOrder, $limitSearch);
    
        $nbenterpriseTotal = $enterpriseResult['length'];
        $enterprises = $enterpriseResult['data'];

        foreach ($enterprises as $enterprise){
            $urlUpdate = $this->generateUrl('enterprise_update', 
                array(
                    'id' => $enterprise['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $urlDelete = $this->generateUrl('enterprise_delete', 
                array(
                    'id' => $enterprise['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $action = "<div class='edit-btn'>
            <button class='btn btn-primary btn-sm dropdown' type='button' id='dropdownMenuButtonAjaxDatatable1' data-bs-toggle='dropdown' aria-expanded='false'><i class='ri-more-fill align-middle'></i></button>            
            <div class='dropdown-menu' aria-labelledby='dropdownMenuButtonAjaxDatatable1'>
                <a class='dropdown-item modify-action' href='".$urlUpdate."'>Modifier</a>
                <a class='dropdown-item delete' href='".$urlDelete."' style='color: red; text-decoration: underline !important;'>Supprimer</a>
            </div>
            </div>";


            $data['data'][] = Array(
                "id" => $enterprise['id'],
                "name" => $enterprise['name'],
                "expertiseField" => $enterprise['expertise_field'],
                "numberDirector" => $enterprise['number_director'],
                "address" => $enterprise['address'],
                "city" => $enterprise['city'],
                "country" => $enterprise['country'],
                "phoneNumber" => $enterprise['phone_number'],
                "siret" => $enterprise['siret'],
                "creationDate" => $enterprise['creation_date'],
                "action" => $action,
            );
        }
        
        $data["draw"] = intval($request->get("draw")); 
        $data["recordsTotal"] = $nbenterpriseTotal; 
        $data["recordsFiltered"] = $nbenterpriseTotal;

        return new JsonResponse($data); 
    }
}
