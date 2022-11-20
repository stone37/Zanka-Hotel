<?php

namespace App\Controller;

use App\Finder\CitiesFinder;
use App\Finder\HostelEquipmentsFinder;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\MultiMatch;
use Elastica\Query\Nested;
use Elastica\Query\Range;
use Elastica\Query\Terms;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private PaginatedFinderInterface $finder;
    private HostelEquipmentsFinder $equipmentsFinder;
    private CitiesFinder $citiesFinder;

    public function __construct(
        PaginatedFinderInterface $finder,
        HostelEquipmentsFinder $equipmentsFinder,
        CitiesFinder $citiesFinder
    )
    {
        $this->finder = $finder;
        $this->equipmentsFinder = $equipmentsFinder;
        $this->citiesFinder = $citiesFinder;
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(): Response
    {
        $boolQuery = new BoolQuery();
        $query = new Query($boolQuery);
        $query->addSort(array('position' => array('order' => 'asc')));

        //$query->setQuery($boolQuery);


        //$query->addSort(['name' => ['order' => 'asc']]);
        /* $fieldQuery = new MatchQuery();
        $fieldQuery->setField('name', 'hotel particulier');
        $boolQuery->addShould($fieldQuery);*/

        /*$priceQuery = new MultiMatch();
        $priceQuery->setFields(['rooms.price'])->setQuery('38000');*/

        /*$priceRange = new Range();
        $priceRange->setParam('rooms.price', ['gte' => 30000]);
        $priceDomainQuery = new Nested();
        $priceDomainQuery->setQuery($priceRange)->setPath('rooms');
        $boolQuery->addFilter($priceDomainQuery);*/

        /*$cityTermQuery = new Terms('location.city.name');
        $cityTermQuery->setTerms(['abidjan']);

        $cityDomainTermQuery = new Nested();
        $cityDomainTermQuery->setQuery($cityTermQuery)->setPath('location.city');

        $locationDomainTermQuery = new Nested();
        $locationDomainTermQuery->setQuery($cityDomainTermQuery)->setPath('location');
        $boolQuery->addMust($locationDomainTermQuery);*/

        //$boolQuery->addShould($locationDomainTermQuery);

        /*$cityQuery = new MultiMatch();
        $cityQuery->setFields(['location.city.name'])->setQuery('abidjan');

        $cityDomainQuery = new Nested();
        $cityDomainQuery->setQuery($cityQuery)->setPath('location.city');

        $locationDomainQuery = new Nested();
        $locationDomainQuery->setQuery($cityDomainQuery)->setPath('location');
        $boolQuery->addShould($locationDomainQuery);*/

        //$results = $this->finder->find($query);
        //dump($results);
        dump($this->citiesFinder->find());


        return $this->render('site/home/index.html.twig');
    }
}

/*$storage->remove();

        $mainBoolQuery = new BoolQuery();
        $boolQuery = new BoolQuery();

        //$fieldQuery = new MatchQuery();
        //$fieldQuery->setField('title', 'Appartement');
        //$boolQuery->addShould($fieldQuery);

       // $tagsQuery = new Query\Terms('tags', ['tag1', 'tag2']);

        /*$boolQuery->addMust(new Terms('subCategoryIds', [14]));

        $nested = new Nested('category');*/

//$slug = 'immobilier';

/*$query = new MultiMatch();
$query->setFields(['category.slug'])->setQuery($slug);*/


//$filtered = new \Elastica\Query\Filtered($subquery);
/*$categoryQuery = new MultiMatch();
$categoryQuery->setFields(['category.slug'])->setQuery($slug);
$categoryDomainQuery = new Nested();
$categoryDomainQuery->setQuery($categoryQuery)->setPath('category');
$boolQuery->addMust($categoryDomainQuery);*/

/*$cityQuery = new MultiMatch();
$cityQuery->setFields(['location.name'])->setQuery('Bouake');
$cityDomainQuery = new Nested();
$cityDomainQuery->setQuery($cityQuery)->setPath('location');
$boolQuery->addShould($cityDomainQuery);*/

/* $boolQuery->addFilter(new Terms('validated', [true]));
 $boolQuery->addFilter(new Terms('denied', [false]));
 $boolQuery->addFilter(new Terms('deleted', [false]));

 $date = (new DateTime())->modify('-6 month');
 $boolQuery->addFilter(new Range('validatedAt', ['gte' => Util::convertDateTimeObject($date)]));*/

/*$cityQuery = new MultiMatch();
$cityQuery->setFields(['location.name'])->setQuery('abidjan');
$cityDomainQuery = new Nested();
$cityDomainQuery->setQuery($cityQuery)->setPath('location');
$boolQuery->addMust($cityDomainQuery);*/


/*$boolQuery->addShould($domainQuery);
$boolQuery->addShould($domainQuery2);*/

//$mainBoolQuery->addMust($boolQuery);

/*$mainBoolQuery->addFilter(new Terms('validated', [true]));
$mainBoolQuery->addFilter(new Terms('denied', [false]));
$mainBoolQuery->addFilter(new Terms('deleted', [false]));*/


/*$data = $finder->findRaw($boolQuery);

dump($data);*/

