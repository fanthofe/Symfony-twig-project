<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectFormType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProjectController extends AbstractController
{
    protected $em;
    
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->em = $projectRepository;
    }

    #[Route('/app/project', name: 'project')]
    public function index(): Response
    {
        $projects = $this->em->findby(['status' => 'ACTIVE']);

        $actions = [
            [
                "title" => "Modifier",
                "link" => 'project_update'
            ],
            [
                "title" => "Supprimer",
                "link" => 'project_delete'
            ]
        ];

        $data = [];
        $columnFr = [
            "ID",
            "Nom",
            "Ressource",
            "Durée",
            "Actions"
        ];

        $projectResult = $this->em->findColumnNameDatabase();
        $projectsColumn = $projectResult['data'];

        foreach ($projectsColumn as $key => $project){
            if(preg_match('/^[a-z]+_[a-z]+$/i', $project['COLUMN_NAME'])){
                $firstName = strstr($project['COLUMN_NAME'], '_', true);
                $secondName = strstr($project['COLUMN_NAME'], '_');
                $reelName = substr($secondName, 1);
                $fullName = strtolower($firstName) . ucfirst($reelName);
                array_push($data, $fullName);
            } else {
                array_push($data, $project['COLUMN_NAME']);
            }
            if($key == count($projectsColumn) - 1){
                array_push($data, "action");
            }
        }

        $urlAdd = $this->generateUrl('project_update', 
        array(
            'id' => 'new-project'
        ), 
        UrlGeneratorInterface::ABSOLUTE_URL);

        $ajaxLink = "project_list_ajax";

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'columns' => $data,
            'ajaxLink' => $ajaxLink,
            'addButton' => $urlAdd,
            "columnFr" => $columnFr,
            'actions' => $actions
        ]);
    }

    #[Route('/app/project/{id}/delete', name: 'project_delete')]
    public function delete(EntityManagerInterface $entityManagerInterface, $id)
    {
        $project = $this->em
            ->find($id);

        if ( !$project ) {
            $this->addFlash('danger', 'Ce projet est introuvable');
            return $this->redirectToRoute('project');
        }

        $project->setStatus('DELETED');
        $project->updatedTimestamps();

        $entityManagerInterface->persist($project);
        $entityManagerInterface->flush();

        $this->addFlash('danger', 'Ce projet a été supprimé');

        return $this->redirectToRoute('project');
    }

    #[Route('/app/project/{id}/update', name: 'project_update')]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, $id)
    {
        $userCurrent = $this->getUser();

        if( $id == 'new-project'){
            $project = new Project;
            $typeForm = "Ajouter";
        } else {
            $project = $this->em
            ->find($id);

            if ( !$project ) {
                $this->addFlash('danger', 'Ce projet est introuvable');
                return $this->redirectToRoute('project');
            }

            $nameTemp = $project->getName();
            $typeForm = "Modifier";
        }

        if( $typeForm == 'Ajouter'){
            $form = $this->createForm(ProjectFormType::class, $project, Array("validation_groups" => "update"));
        } else {
            $form = $this->createForm(ProjectFormType::class, $project, Array("validation_groups" => "update"));
        }

        $formValid = null;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formValid = true;

            if($typeForm == 'Ajouter'){
                $nameExist = $this->em
                ->findBy(['name' => $project->getName()]);

                $exist = false;
                if($nameExist){
                    foreach($nameExist as $name){
                        if($name->getStatus() == "ACTIVE"){
                            $exist = true;
                        }
                    }

                    if($exist == true){
                        $this->addFlash('danger', 'Ce projet a déjà été pris');
                        $formValid = false;
                    }
                }

                $project->setStatus('ACTIVE');
            } else {

                if($nameTemp != $project->getName()){
                    $nameExist = $this->em
                        ->findBy(['name' => $project->getName()]);

                    $exist = false;

                    if ( $nameExist ) {
                        foreach ($nameExist as $name) {
                            if($name->getStatus() == "ACTIVE"){
                                $exist = true;
                            }
                        }

                        if($exist == true){
                            $this->addFlash('danger', 'Ce projet a déjà été pris');
                            $formValid = false;
                        }
                    }
                }
            }

            if($formValid){

                if ( $typeForm == 'Ajouter' ){
                    $project->setCreatedBy($userCurrent)
                    ->setCreatedAt(new \DateTime('now'));
                }

                $project->updatedTimestamps();

                $entityManagerInterface->persist($project);
                $entityManagerInterface->flush();

                if($typeForm == 'Ajouter'){
                    $this->addFlash('success', "Le projet a été ajouté");
                } else {
                    $this->addFlash('success', 'Le projet a été modifié');
                }

                return $this->redirectToRoute('project');
            }
        }
        
        return $this->render('project/update.html.twig', Array(
            "project" => $project,
            "projectId" => $id,
            "form" => $form->createView(),
            "typeForm" => $typeForm
        ));
    }

    #[Route('/app/project-ajax', name: 'project_list_ajax')]
    public function listAjax(Request $request): Response
    {
        //Load dataTable request
        $limitStart = $request->get("start");
        $limitWidth = $request->get("length");
        $limitSearch = $request->get("search");
        $limitOrder = $request->get("order");
        
        $data['data'] = [];

        $projectResult = $this->em->findAllAjax($limitStart, $limitWidth, $limitOrder, $limitSearch);
    
        $nbprojectTotal = $projectResult['length'];
        $projects = $projectResult['data'];

        foreach ($projects as $project){

            $urlUpdate = $this->generateUrl('project_update', 
                array(
                    'id' => $project['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $urlDelete = $this->generateUrl('project_delete', 
                array(
                    'id' => $project['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $action = "<div class='edit-btn'>
            <button class='btn btn-primary btn-sm dropdown' type='button' id='dropdownMenuButtonAjaxDatatable2' data-bs-toggle='dropdown' aria-expanded='false'><i class='ri-more-fill align-middle'></i></button>            
            <div class='dropdown-menu' aria-labelledby='dropdownMenuButtonAjaxDatatable2'>
                <a class='dropdown-item' href='".$urlUpdate."'>Modifier</a>
                <a class='dropdown-item delete' href='' style='color: red; text-decoration: underline !important;'>Supprimer</a>
            </div>
            </div>";


            $data['data'][] = Array(
                "id" => $project['id'],
                "name" => $project['name'],
                "ressource" => $project['ressource'],
                "estimationDuration" => $project['estimation_duration'],
                "action" => $action,
            );
        }
        
        $data["draw"] = intval($request->get("draw")); 
        $data["recordsTotal"] = $nbprojectTotal; 
        $data["recordsFiltered"] = $nbprojectTotal;

        return new JsonResponse($data); 
    }
}
