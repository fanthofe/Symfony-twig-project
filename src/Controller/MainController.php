<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\LocaleSwitcher;

class MainController extends AbstractController
{
    public function __construct(
        private LocaleSwitcher $localeSwitcher
    ){
    }

    #[Route('/switch-locale/{locale}', name: 'switch_locale')]
    public function switchLocal($locale, Request $request)
    {
        $request->getSession()->set('_locale', $locale);

        return $this->redirect($request->headers->get('referer'));
    }
}