<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinksController extends AbstractController
{
    /**
     * @return Response
     * @Route("/")
     */
    public function homePage(): Response
    {
        return $this->render('pages/homePage.html.twig');
    }
}