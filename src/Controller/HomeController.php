<?php

namespace App\Controller;

use App\Storage\BookingStorage;
use App\Storage\CartStorage;
use App\Storage\CommandeStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private BookingStorage $storage,
        private CartStorage $cartStorage,
        private CommandeStorage $commandeStorage
    )
    {
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(): Response
    {
        $this->storage->remove();
        $this->storage->removeData();
        $this->storage->removeId();
        $this->cartStorage->init();
        $this->commandeStorage->remove();

        return $this->render('site/home/index.html.twig');
    }
}
