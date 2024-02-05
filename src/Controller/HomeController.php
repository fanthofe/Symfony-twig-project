<?php

namespace App\Controller;

use App\Repository\SettingsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    protected $em;
    protected $settingsRepo;

    public function __construct(Environment $twig, UserRepository $userRepository, SettingsRepository $settingsRepository)
    {
        $this->loader = $twig->getLoader();
        $this->em = $userRepository;
        $this->settingsRepo = $settingsRepository;
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $settings = $this->settingsRepo->find(['id' => 1]);

        if($settings->isIsInMaintenance()){
            return $this->render('maintenance/index.html.twig');
        }

        return $this->render('landing/index.html.twig');
    }

    #[Route('/app', name: 'app_home')]
    public function index(): Response
    {
        $users = $this->em->findDisplayUser();

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

        $userResult = $this->em->findColumnNameDatabase();
        $usersColumn = $userResult['data'];

        foreach ($usersColumn as $key => $user){
            if(preg_match('/^[a-z]+_[a-z]+$/i', $user['COLUMN_NAME'])){
                $firstName = strstr($user['COLUMN_NAME'], '_', true);
                $secondName = strstr($user['COLUMN_NAME'], '_');
                $reelName = substr($secondName, 1);
                $fullName = strtolower($firstName) . ucfirst($reelName);
                array_push($data, $fullName);
            } else {
                array_push($data, $user['COLUMN_NAME']);
            }
            if($key == count($usersColumn) - 1){
                array_push($data, "action");
            }
        }

        $urlAdd = $this->generateUrl('template_preview_home', 
        array(
            'id' => 'new-user'
        ), 
        UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->render('index.html.twig', [
            'users' => $users,
            'columns' => $data,
            'addButton' => $urlAdd,
            'actions' => $actions
        ]);
    }

    #[Route('/template-preview/', name: 'template_preview_home')]
    public function templatePreviewHome(): Response
    {
        return $this->render('templatePreview/index.html.twig');
    }

    #[Route('/template-preview/{path}', name: 'template_preview')]
    public function templatePreview($path)
    {
        if ($this->loader->exists("templatePreview/".$path.'.html.twig')) {
            if ($path == '/' || $path == 'home') {
                die('Home');
            }
            return $this->render("templatePreview/".$path.'.html.twig');
        }
        throw $this->createNotFoundException();
    }
}
