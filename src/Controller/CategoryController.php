<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryController extends AbstractController
{
    private $em;

    public function __construct(CategoryRepository $categoryRepository){
        $this->em = $categoryRepository;
    }

    #[Route('/app/category', name: 'category')]
    public function index()
    {
        $categories = $this->em->findAll();

        $actions = [
            [
                "title" => "Modifier",
                "link" => 'category_update'
            ],
            [
                "title" => "Supprimer",
                "link" => 'category_delete'
            ]
        ];

        $columnFr = [
            "ID",
            "Nom",
            "Actions"
        ];

        $data = [
            "id",
            "name",
            "action"
        ];

        $urlAdd = $this->generateUrl('category_update', 
        array(
            'id' => 'new-category'
        ), 
        UrlGeneratorInterface::ABSOLUTE_URL);

        $ajaxLink = "category_list_ajax";
        $indexPath = "category";

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'columns' => $data,
            "columnFr" => $columnFr,
            'ajaxLink' => $ajaxLink,
            'addButton' => $urlAdd,
            'path' => $indexPath,
            'actions' => $actions
        ]);
    }

    #[Route('/app/category/{id}/delete', name: 'category_delete')]
    public function delete(EntityManagerInterface $entityManagerInterface, $id)
    {
        $category = $this->em
            ->find($id);

        if ( !$category ) {
            $this->addFlash('danger', 'Cette catégorie est introuvable');
            return $this->redirectToRoute('category');
        }

        $category->setStatus('DELETED');
        $category->updatedTimestamps();

        $entityManagerInterface->persist($category);
        $entityManagerInterface->flush();

        $this->addFlash('danger', 'Cette catégorie a été supprimée');

        return $this->redirectToRoute('category');
    }

    #[Route('/app/category/{id}/update', name: 'category_update')]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, $id)
    {
        if( $id == 'new-category'){
            $category = new Category;
            $typeForm = "Ajouter";
        } else {
            $category = $this->em
            ->find($id);

            if ( !$category ) {
                $this->addFlash('danger', 'Cette catégorie est introuvable');
                return $this->redirectToRoute('category');
            }

            $nameTemp = $category->getName();
            $typeForm = "Modifier";
        }

        if( $typeForm == 'Ajouter'){
            $form = $this->createForm(CategoryFormType::class, $category, Array("validation_groups" => "update"));
        } else {
            $form = $this->createForm(CategoryFormType::class, $category, Array("validation_groups" => "update"));
        }

        $formValid = null;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formValid = true;

            if($typeForm == 'Ajouter'){
                $nameExist = $this->em
                ->findBy(['name' => $category->getName()]);

                $exist = false;
                if($nameExist){
                    foreach($nameExist as $name){
                        if($name->getStatus() == "ACTIVE"){
                            $exist = true;
                        }
                    }

                    if($exist == true){
                        $this->addFlash('danger', 'Cette catégorie est déjà pris');
                        $formValid = false;
                        return $this->redirectToRoute('category');
                    }
                }

                $category->setStatus('ACTIVE');
            } else {

                if($nameTemp != $category->getName()){
                    $nameExist = $this->em
                        ->findBy(['name' => $category->getName()]);

                    $exist = false;

                    if ( $nameExist ) {
                        foreach ($nameExist as $name) {
                            if($name->getStatus() == "ACTIVE"){
                                $exist = true;
                            }
                        }

                        if($exist == true){
                            $this->addFlash('danger', 'Cette catégorie est déjà pris');
                            $formValid = false;
                            return $this->redirectToRoute('category');
                        }
                    }
                }
            }

            if($formValid){

                $category->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
                $category->updatedTimestamps();

                $entityManagerInterface->persist($category);
                $entityManagerInterface->flush();

                if($typeForm == 'Ajouter'){
                    $this->addFlash('success', "La catégorie a été ajoutée");
                } else {
                    $this->addFlash('success', "La catégorie a été modifiée");
                }

                return $this->redirectToRoute('category');
            }
        }
        
        return $this->render('category/update.html.twig', Array(
            "category" => $category,
            "categoryId" => $id,
            "form" => $form->createView(),
            "typeForm" => $typeForm
        ));
    }

    #[Route('/app/category-ajax', name: 'category_list_ajax')]
    public function listAjax(Request $request): Response
    {
        //Load dataTable request
        $limitStart = $request->get("start");
        $limitWidth = $request->get("length");
        $limitSearch = $request->get("search");
        $limitOrder = $request->get("order");
        
        $data['data'] = [];

        $categoryResult = $this->em->findAllAjax($limitStart, $limitWidth, $limitOrder, $limitSearch);
    
        $nbcategoryTotal = $categoryResult['length'];
        $categorys = $categoryResult['data'];

        foreach ($categorys as $category){
            $urlUpdate = $this->generateUrl('category_update', 
                array(
                    'id' => $category['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $urlDelete = $this->generateUrl('category_delete', 
                array(
                    'id' => $category['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $action = "<div class='edit-btn'>
            <button class='btn btn-primary btn-sm dropdown' type='button' id='dropdownMenuButtonAjaxDatatable1' data-bs-toggle='dropdown' aria-expanded='false'><i class='ri-more-fill align-middle'></i></button>            
            <div class='dropdown-menu' aria-labelledby='dropdownMenuButtonAjaxDatatable1'>
                <a class='ajax-edit-btn dropdown-item' href='".$urlUpdate."'>Modifier</a>
                <a class='dropdown-item delete' href='".$urlDelete."' style='color: red; text-decoration: underline !important;'>Supprimer</a>
            </div>
            </div>";

            $data['data'][] = Array(
                "id" => $category['id'],
                "name" => $category['name'],
                "action" => $action,
            );
        }
        
        $data["draw"] = intval($request->get("draw")); 
        $data["recordsTotal"] = $nbcategoryTotal; 
        $data["recordsFiltered"] = $nbcategoryTotal;

        return new JsonResponse($data); 
    }
}