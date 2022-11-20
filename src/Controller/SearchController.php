<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Category;
use App\Form\SearchGlobalMobileType;
use App\Form\SearchGlobalType;
use App\Manager\AdvertManager;
use App\Model\Search;
use App\Model\SearchInterface;
use App\Repository\AdvertRepository;
use App\Service\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Knp\Component\Pager\Event\Subscriber\Paginate\Callback\CallbackPagination;

class SearchController extends AbstractController
{
    use ControllerTrait;

    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    public function index(Request $request, EntityManagerInterface $em)
    {
        $search = new Search();

        $form = $this->createForm(SearchGlobalType::class, $search, [
            'em' => $em,
            'action' => $this->generateUrl('app_search_index')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($search->getCategory()) {
                $category = $em->getRepository(Category::class)->find((int)$search->getCategory());

                return $this->redirectToRoute('app_advert_index', [
                    'category_slug' => $category->getSlug(),
                    'data' => $search->getData(),
                ]);
            } else {
                return $this->redirectToRoute('app_search_result', ['data' => $search->getData()]);
            }
        }

        return $this->render('site/search/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function indexM(Request $request, EntityManagerInterface $em)
    {
        $search = new Search();

        $form = $this->createForm(SearchGlobalMobileType::class, $search, [
            'em' => $em,
            'action' => $this->generateUrl('app_search_index_m')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($search->getCategory()) {
                $category = $em->getRepository(Category::class)->find((int)$search->getCategory());

                return $this->redirectToRoute('app_advert_index', [
                    'category_slug' => $category->getSlug(),
                    'data' => $search->getData(),
                ]);
            } else {
                return $this->redirectToRoute('app_search_result', ['data' => $search->getData()]);
            }
        }

        return $this->render('site/search/index_m.html.twig', [
            'form_mobile' => $form->createView()
        ]);
    }

    public function result(
        Request $request,
        EntityManagerInterface $em,
        Breadcrumbs $breadcrumbs,
        AdvertManager $manager,
        PaginatorInterface $paginator,
        SettingsManager $sm)
    {
        $breadcrumbs->addItem('Accueil', $this->generateUrl('app_home'))
                    ->addItem('Liste d\'annonce');

        $search = new Search();
        $search->setData($request->query->get('data'));

        $qb = $manager->getAdvertLists($search);
        $adverts = $paginator->paginate($qb, $request->query->getInt('page', 1), 15);

        return $this->render('site/search/result.html.twig', [
            'adverts' => $adverts,
            'settings' => $this->getSettings($sm),
            'categories' => $this->getCategories($em),
        ]);
    }

    public function location(EntityManagerInterface $em)
    {
        $code = 'ci';

        $areas = [];
        $cities = [];

        return $this->render('site/search/location.html.twig', [
            'areas' => $areas,
            'cities' => $cities,
        ]);
    }

    public function searchArea(EntityManagerInterface $em, SessionInterface $session, $id)
    {
        $cities = [];

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getName();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer], [$encoder]);
        $response = $serializer->serialize($cities, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['createdAt', 'updatedAt']]);

        $session->set('app_user_search_area', $id);

        return new JsonResponse($response);
    }

    public function searchCity(EntityManagerInterface $em, SessionInterface $session, $id)
    {
        $districts = [];

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getName();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer], [$encoder]);
        $response = $serializer->serialize($districts, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['createdAt', 'updatedAt']]);

        $session->set('app_user_search_city', $id);

        return new JsonResponse($response);
    }

    public function searchDistrict(SessionInterface $session, $id)
    {
        $session->set('app_user_search_district', $id);

        return new JsonResponse(true);
    }

    public function search(
        Request $request,
        SearchInterface $search,
        NormalizerInterface $normalizer,
        AdvertRepository $advertRepository
    ): Response {
        $q = $request->query->get('q', '');
        $redirect = $request->query->get('redirect', '1');

        if (!empty($q) && '0' !== $redirect) {
            $adverts = $advertRepository->findByTitle($q);

            if (null !== $adverts) {
                /** @var array{'path': string, "params": string[]} $path */
                $path = $normalizer->normalize($adverts, 'path');

                return $this->redirectToRoute(
                    $path['path'],
                    $path['params']
                );
            }
        }

        $page = (int) $request->get('page', 1) ?: 1;
        $results = $search->search($q, [], 10, $page);
        $paginableResults = new CallbackPagination(function ($results) {return $results->getTotal();}, function ($results) {$results->getItems();});

        return $this->render('site/search/result.html.twig', [
            'q' => $q,
            'total' => $results->getTotal(),
            'results' => $this->paginator->paginate($paginableResults, $page),
        ]);
    }



}
