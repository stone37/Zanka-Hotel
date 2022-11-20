<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Génère le body de la page d'erreur.
 */
class ErrorController extends AbstractController
{
    public function body(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/_body.html.twig', []);
    }
}
