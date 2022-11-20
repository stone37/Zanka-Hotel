<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    use ControllerTrait;

    public function dropdownMenu()
    {
        return $this->render('site/menu/dropdown.html.twig', ['user' => $this->getUserOrThrow()]);
    }
}

