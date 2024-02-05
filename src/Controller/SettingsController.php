<?php

namespace App\Controller;

use App\Form\SettingsFormType;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SettingsController extends AbstractController
{
    protected $em;
    
    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->em = $settingsRepository;
    }

    #[Route('/app/settings', name: 'settings')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $settings = $this->em->getOnlyOneSetting();
        $form = $this->createForm(SettingsFormType::class, $settings);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $settings->setCreatedAt(new \DateTime('now'))
            ->updatedTimestamps();

            $entityManagerInterface->persist($settings);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('settings');
        }

        return $this->render('settings/index.html.twig', [
            "form" => $form->createView(),
        ]);
    }
}
