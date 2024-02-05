<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Form\ArticleFormType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    private $em;

    public function __construct(ArticleRepository $articleRepository){
        $this->em = $articleRepository;
    }

    #[Route('/app/articles', name: 'article')]
    public function index()
    {
        $articles = $this->em->findBy(['dataStatus' => 'ACTIVE']);

        $actions = [
            [
                "title" => "Modifier",
                "link" => 'article_update'
            ],
            [
                "title" => "Supprimer",
                "link" => 'article_delete'
            ]
        ];

        $data = [];
        $columnFr = [
            "ID",
            "Titre",
            "Date",
            "Status",
            "Actions"
        ];

        $articleResult = $this->em->findColumnNameDatabase();
        $articlesColumn = $articleResult['data'];

        foreach ($articlesColumn as $key => $article){
            if(preg_match('/^[a-z]+_[a-z]+$/i', $article['COLUMN_NAME'])){
                $firstName = strstr($article['COLUMN_NAME'], '_', true);
                $secondName = strstr($article['COLUMN_NAME'], '_');
                $reelName = substr($secondName, 1);
                $fullName = strtolower($firstName) . ucfirst($reelName);
                array_push($data, $fullName);
            } else {
                array_push($data, $article['COLUMN_NAME']);
            }
            if($key == count($articlesColumn) - 1){
                array_push($data, "action");
            }
        }

        $urlAdd = $this->generateUrl('article_update', 
        array(
            'id' => 'new-article'
        ), 
        UrlGeneratorInterface::ABSOLUTE_URL);

        $ajaxLink = "article_list_ajax";

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'columns' => $data,
            "columnFr" => $columnFr,
            'ajaxLink' => $ajaxLink,
            'addButton' => $urlAdd,
            'actions' => $actions
        ]);
    }

    #[Route('/app/list-articles', name: 'list_article')]
    public function listArticles()
    {
        $articles = $this->em->findBy(['dataStatus' => 'ACTIVE']);
        $pageTitle = "Listes d'articles";

        return $this->render('article/list-articles.html.twig', [
            'articles' => $articles,
            'pageTitle' => $pageTitle,
        ]);
    }

    #[Route('/article/{slug}', name: 'single_article')]
    public function getSingleArticle($slug)
    {
        $article = $this->em->findOneBy(['slug' => $slug]);

        return $this->render('article/single-article.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/actualites', name: 'actuality')]
    public function actuality()
    {
        $articles = $this->em->findArticlesPagination(1, 4);
        $pageTitle = "Actualités";

        return $this->render('article/actuality.html.twig', [
            'articles' => $articles,
            'pageTitle' => $pageTitle,
            'nbAllArticles' => $this->em->lengthAllArticles()
        ]);
    }

    #[Route('/actualites/pagination-ajax', name: 'actuality_pagination_ajax')]
    public function pagination(Request $request): Response
    {
        $params = $request->request->all();

        $articles = $this->em->findArticlesPagination($params['currentPage'], $params['limit']);

        $result = [
            'data' => [],
            'pages' => 0,
            'page' => 0,
            'limit' => 0,
        ];

        foreach($articles['data'] as $article){
            array_push($result['data'], [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'slug' => $article->getSlug(),
                'shortDescription' => $article->getShortDescription(),
                'date' => $article->getDate(),
                'image' => $article->getImage(),
                'category' => []
            ]);
        }

        for($i = 0; $i < count($articles['data']); $i++){
            foreach($articles['data'][$i]->getArticleCategories() as $category){
                array_push($result['data'][$i]['category'], [
                    'id' => $category->getCategory()->getId(),
                    'name' => $category->getCategory()->getName(),
                ]);
            }
        }
        
        $result['pages'] = $articles['pages'];
        $result['page'] = $articles['page'];
        $result['limit'] = count($result['data']) != $articles['limit'] ? count($result['data']) : $articles['limit'];

        return new JsonResponse($result);
    }

    #[Route('/app/article/{id}/delete', name: 'article_delete')]
    public function delete(EntityManagerInterface $entityManagerInterface, $id)
    {
        $article = $this->em
            ->find($id);

        if ( !$article ) {
            $this->addFlash('danger', 'Cet article est introuvable');
            return $this->redirectToRoute('article');
        }

        $article->setDataStatus('DELETED');
        $article->updatedTimestamps();

        $entityManagerInterface->persist($article);
        $entityManagerInterface->flush();

        $this->addFlash('danger', 'Cet article a été supprimé');

        return $this->redirectToRoute('article');
    }

    #[Route('/app/article/{id}/update', name: 'article_update')]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, $id, SluggerInterface $slugger)
    {
        if( $id == 'new-article'){
            $article = new Article;
            $typeForm = "Ajouter";
        } else {
            $article = $this->em
            ->find($id);

            if ( !$article ) {
                $this->addFlash('danger', 'Cet article est introuvable');
                return $this->redirectToRoute('article');
            }

            $typeForm = "Modifier";
        }

        if( $typeForm == 'Ajouter'){
            $form = $this->createForm(ArticleFormType::class, $article, Array("validation_groups" => "update"));
            
        } else {
            $form = $this->createForm(ArticleFormType::class, $article, Array("validation_groups" => "update"));
        }

        $formValid = null;

        $form->handleRequest($request);

        
        if($form->isSubmitted() && $form->isValid()){

            $formValid = true;

            foreach($form->all()['articleCategories']->getData() as $category){
                if(!in_array($category->getName(), $article->getCategory())){
                    $articleCategory = new ArticleCategory;
    
                    $articleCategory->setCategory($category);
                    $articleCategory->setArticle($article);
                    $articleCategory->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
                    $articleCategory->updatedTimestamps();
                    
                    
                    $entityManagerInterface->persist($articleCategory);
                }
            }

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                $extension = $imageFile->guessExtension();
                
                // Move the file to the directory where brochures are stored
                try {
                    // Save image in the directory without customize image quality
                    $imageFile->move(
                        $this->getParameter('article_file_directory'),
                        $newFilename
                    );
                
                    // $chemin = $this->getParameter('article_file_directory').'/'.$newFilename; // le chemin en absolu

                    // Use the code below if you want to customize image's width and height
                    //Y
                    // $this->convertImage($chemin, $this->getParameter('article_file_directory').'/small-'.$newFilename, 300, 461, 100, $extension);
                    // $this->convertImage($chemin, $this->getParameter('article_file_directory').'/'.$newFilename, 600, 920, 100, $extension);

                    //X
                    // $this->convertImage($chemin, $this->getParameter('article_file_directory').'/-x-small-'.$newFilename, 366, 245, 100, $extension);
                    // $this->convertImage($chemin, $this->getParameter('article_file_directory').'/-x-'.$newFilename, 700, 468.57, 100, $extension);


                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw new \ErrorException($e->getMessage());
                }

                // updates the 'imageFilename' property to store the PDF file name
                // instead of its contents
                $article->setImage($newFilename);
            }
            $article->setSlug($this->createSlug($form, $slugger));

            if($typeForm == 'Ajouter'){
                $article->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
                $article->updatedTimestamps();
            } else {
                $article->updatedTimestamps();
            }

            if($formValid){
                
                $entityManagerInterface->persist($article);
                $entityManagerInterface->flush();

                if($typeForm == 'Ajouter'){
                    $this->addFlash('success', "L'article a été ajouté");
                } else {
                    $this->addFlash('success', "L'article a été modifié");
                }
                return $this->redirectToRoute('article');
            }
        }
        
        return $this->render('article/update.html.twig', Array(
            "article" => $article,
            "articleId" => $id,
            "form" => $form->createView(),
            "typeForm" => $typeForm
        ));
    }

    #[Route('/app/article-ajax', name: 'article_list_ajax')]
    public function listAjax(Request $request): Response
    {
        //Load dataTable request
        $limitStart = $request->get("start");
        $limitWidth = $request->get("length");
        $limitSearch = $request->get("search");
        $limitOrder = $request->get("order");
        
        $data['data'] = [];

        $articleResult = $this->em->findAllAjax($limitStart, $limitWidth, $limitOrder, $limitSearch);
    
        $nbarticleTotal = $articleResult['length'];
        $articles = $articleResult['data'];

        foreach ($articles as $article){
            $urlUpdate = $this->generateUrl('article_update', 
                array(
                    'id' => $article['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $urlDelete = $this->generateUrl('article_delete', 
                array(
                    'id' => $article['id']
                ), 
            UrlGeneratorInterface::ABSOLUTE_URL);

            $action = "<div class='edit-btn'>
            <button class='btn btn-primary btn-sm dropdown' type='button' id='dropdownMenuButtonAjaxDatatable1' data-bs-toggle='dropdown' aria-expanded='false'><i class='ri-more-fill align-middle'></i></button>            
            <div class='dropdown-menu' aria-labelledby='dropdownMenuButtonAjaxDatatable1'>
                <a class='dropdown-item' href='".$urlUpdate."'>Modifier</a>
                <a class='dropdown-item delete' href='".$urlDelete."' style='color: red; text-decoration: underline !important;'>Supprimer</a>
            </div>
            </div>";

            $date = new \DateTime($article['date']);
            $formatter = new \IntlDateFormatter(\Locale::getDefault(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
            $formatter->setPattern('d MMM Y');

            $data['data'][] = Array(
                "id" => $article['id'],
                "title" => $article['title'],
                "date" => $formatter->format($date),
                "status" => $article['status'] == 'PUBLISHED' ? 'PUBLIE' : 'BROUILLON',
                "action" => $action,
            );
        }

        
        $data["draw"] = intval($request->get("draw")); 
        $data["recordsTotal"] = $nbarticleTotal; 
        $data["recordsFiltered"] = $nbarticleTotal;

        return new JsonResponse($data); 
    }

    function convertImage($source, $dst, $width, $height, $quality, $type){

        $imageSize = getimagesize($source);
        if($type == "png"){
            $imageRessource= imagecreatefrompng($source) ;
        }
        else{
            $imageRessource= imagecreatefromjpeg($source) ;
        }

        // old images width will fit
        if(($imageSize[0] / $imageSize[1]) < ($width/$height)){
            $scale = $width/$imageSize[0];
            $newX = 0;
            $newY = - ($scale * $imageSize[1] - $height) / 2;

        // else old image's height will fit
        }else{
            $scale = $height/$imageSize[1];
            $newX = - ($scale * $imageSize[0] - $width) / 2;
            $newY = 0;
        }
      
      $imageFinal = imagecreatetruecolor($width, $height) ;
      $final = imagecopyresampled($imageFinal, $imageRessource, $newX, $newY, 0, 0, $scale * $imageSize[0], $scale * $imageSize[1], $imageSize[0], $imageSize[1]) ;
      
      if($type == "png"){
        imagepng($imageFinal, $dst, 9, PNG_FILTER_PAETH) ;
      }
      else{
        imagejpeg($imageFinal, $dst, $quality) ;
      }
    } 

    public function createSlug(FormInterface $form, SluggerInterface $slugger){
        $newArticleSlug = $slugger->slug(strtolower($form->get('title')->getData()));
        $slugCollection = $this->em->checkExistingSlug($newArticleSlug);

        if (count($slugCollection) == 0){
            return $newArticleSlug;
        } else {
            $range = range(1, max($slugCollection));
            $diffSlugCollection = array_diff($range, $slugCollection);

            
            if(count($diffSlugCollection) > 0){

                if(array_values($diffSlugCollection)[0] == 1){
                    return $newArticleSlug;
                } else {

                    return $newArticleSlug . "-" . strval(array_values($diffSlugCollection)[0]);
                }
            } else {
                return $newArticleSlug . "-" . strval(max($slugCollection) + 1);
            }
        }
    }
}