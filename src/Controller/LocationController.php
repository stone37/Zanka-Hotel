<?php

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LocationController extends AbstractController
{
    private CityRepository $repository;

    public function __construct(CityRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route(path: '/cities/search', name: 'app_city_search', options: ['expose' => true])]
    public function search(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Resource introuvable');
        }

        $query = $request->request->get('q');

        $cities = $this->repository->search($query);

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer(
            null,
            null,
            null,
            null,
            null,
            null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER =>
                function ($object, $format, $context) {return $object->getId();},
        ]);

        $serializer = new Serializer([$normalizer], [$encoder]);
        $response = $serializer->serialize($cities, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['createdAt', 'updatedAt']]);

        return new JsonResponse($response);
    }


    public function partial()
    {
        return $this->render('site/menu/_locationPopular.html.twig', [
            'cities' => $this->repository->getPartial()
        ]);
    }

    public function home()
    {
        return $this->render('site/location/_homePopular.html.twig', [
            'cities' => $this->repository->getPartial(10)
        ]);
    }
}

