<?php

namespace App\Controller;

use App\Manager\BannerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BannerController extends AbstractController
{
    private BannerManager $manager;

    public function __construct(BannerManager $manager)
    {
        $this->manager = $manager;
    }

    public function show(string $type, string $location)
    {
        return $this->render('site/banner/show_' . strtolower($type) . '.html.twig', [
            'banners' => $this->manager->get($type, $location)
        ]);
    }

    #[Route(path: '/switch-banner', name: 'app_switch_banner', options: ['expose' => true])]
    public function switch(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Resource introuvable');
        }

        $id = $request->request->get('id');

        if ($this->manager->has($id)) {
            return new JsonResponse(false);
        }

        $this->manager->add($request->request->get('id'));

        return new JsonResponse(true);
    }
}


