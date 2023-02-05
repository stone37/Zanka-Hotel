<?php

namespace App\Repository;

use App\Controller\RequestDataHandler\HostelSortDataHandler;
use App\Data\BookingData;
use App\Entity\City;
use App\Entity\Hostel;
use App\Entity\User;
use App\PropertyNameResolver\PriceNameResolver;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\Admin\HostelAdminSearch;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Hostel>
 *
 * @method Hostel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hostel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hostel[]    findAll()
 * @method Hostel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HostelRepository extends AbstractRepository
{
    private PriceNameResolver $priceNameResolver;
    private ?string $namePropertyPrefix;
    private ?string $cityPropertyPrefix;
    private ?string $equipmentPropertyPrefix;
    private ?string $roomEquipmentPropertyPrefix;
    private ?string $categoryPropertyPrefix;
    private ?string $starNumberPropertyPrefix;
    private ?string $averageRatingPropertyPrefix;
    private ?string $occupantPropertyPrefix;

    public function __construct(
        ManagerRegistry $registry,
        ParameterBagInterface $parameterBag,
        PriceNameResolver $priceNameResolver
    )
    {
        parent::__construct($registry, Hostel::class);

        $this->priceNameResolver = $priceNameResolver;
        $this->namePropertyPrefix = $parameterBag->get('app_name_property_prefix');
        $this->cityPropertyPrefix = $parameterBag->get('app_city_property_prefix');
        $this->equipmentPropertyPrefix = $parameterBag->get('app_equipment_property_prefix');
        $this->roomEquipmentPropertyPrefix = $parameterBag->get('app_room_equipment_property_prefix');
        $this->categoryPropertyPrefix = $parameterBag->get('app_category_property_prefix');
        $this->starNumberPropertyPrefix = $parameterBag->get('app_star_number_property_prefix');
        $this->averageRatingPropertyPrefix = $parameterBag->get('app_average_rating_property_prefix');
        $this->occupantPropertyPrefix = $parameterBag->get('app_occupant_property_prefix');
    }

    public function add(Hostel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Hostel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getWithFilter(): ?array
    {
        $results = $this->createQueryBuilder('h')
                ->andWhere('h.enabled = 1')
                ->orderBy('h.position', 'asc')
                ->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['name']] = $result['id'];
        }

        return $data;
    }

    public function getWithPartnerFilter(User|UserInterface $user): ?array
    {
        $results = $this->createQueryBuilder('h')
                ->where('h.owner = :user')
                ->setParameter('user', $user)
                ->orderBy('h.position', 'asc')
                ->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['name']] = $result['id'];
        }

        return $data;
    }

    public function getAdmins(HostelAdminSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.rooms', 'rooms')
            ->leftJoin('h.category', 'category')
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('h.owner', 'user')
            ->leftJoin('h.bookings', 'bookings')
            ->leftJoin('h.reviews', 'reviews')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('equipments')
            ->addSelect('user')
            ->addSelect('bookings')
            ->addSelect('reviews')
            ->orderBy('h.position', 'asc');

        if ($search->isEnabled()) {
            $qb->andWhere('h.enabled = 1');
        }

        if ($search->getName()) {
            $qb->andWhere('h.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        if ($search->getCategory()) {
            $qb->andWhere('h.category = :category')->setParameter('category', $search->getCategory());
        }

        if ($search->getEmail()) {
            $qb->andWhere('h.email = :email')->setParameter('email', $search->getEmail());
        }

        return $qb;
    }

    public function getByPartner(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.rooms', 'rooms')
            ->leftJoin('h.category', 'category')
            ->leftJoin('h.location', 'location')
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('h.owner', 'owner')
            ->leftJoin('h.bookings', 'bookings')
            ->leftJoin('h.reviews', 'reviews')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('location')
            ->addSelect('equipments')
            ->addSelect('owner')
            ->addSelect('bookings')
            ->addSelect('reviews')
            ->where('h.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('h.position', 'asc');

        return $qb;
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.enabled = 1')
            ->andWhere('h.closed = 0')
            ->getQuery()
            ->getResult();
    }

    public function getTotalEnabled(array $data): QueryBuilder
    {
        $qb = $this->createQueryBuilder('h');
        $qb->join('h.rooms', 'rooms', 'WITH', 'rooms.enabled = 1')
            ->leftJoin('h.category', 'category')
            ->leftJoin('h.location', 'location')
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('h.galleries', 'galleries')
            ->leftJoin('rooms.equipments', 'roomEquipment')
            ->leftJoin('rooms.promotions', 'promotions')
            ->leftJoin('promotions.action', 'action')
            ->leftJoin('rooms.taxes', 'taxes')
            ->leftJoin('h.favorites', 'favorites')
            ->leftJoin('h.cancellationPolicy', 'cancellationPolicy')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('location')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('roomEquipment')
            ->addSelect('promotions')
            ->addSelect('action')
            ->addSelect('taxes')
            ->addSelect('favorites')
            ->addSelect('cancellationPolicy')
            ->where('h.enabled = 1')
            ->andWhere('h.closed = 0')
            ->andWhere('location.city = :city')
            ->setParameter('city', $data[$this->cityPropertyPrefix]);

        $qb = $this->addFilterData($qb, $data);
        $qb = $this->addSortData($qb, $data);

        return $qb;
    }

    public function getEnabled(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.rooms', 'rooms')
            ->leftJoin('h.category', 'category')
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('rooms.equipments', 'roomEquipment')
            ->leftJoin('h.owner', 'user')
            ->leftJoin('h.bookings', 'bookings')
            ->leftJoin('h.favorites', 'favorites')
            ->leftJoin('h.reviews', 'reviews')
            ->leftJoin('h.cancellationPolicy', 'cancellationPolicy')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('equipments')
            ->addSelect('user')
            ->addSelect('bookings')
            ->addSelect('favorites')
            ->addSelect('reviews')
            ->addSelect('cancellationPolicy')
            ->orderBy('h.position', 'asc')
        ;

        return $qb;
    }

    public function getEnabledBySlug(BookingData $data, string $slug): ?Hostel
    {
        $occupant = $data->adult + $data->children;

        $qb = $this->createQueryBuilder('h');
        $qb->join('h.rooms', 'rooms', 'WITH', 'rooms.enabled = 1')
            ->leftJoin('h.category', 'category')
            ->leftJoin('h.location', 'location')
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('equipments.equipmentGroup', 'equipmentGroup')
            ->leftJoin('h.galleries', 'galleries')
            ->leftJoin('rooms.equipments', 'roomEquipment')
            ->leftJoin('roomEquipment.roomEquipmentGroup', 'roomEquipmentGroup')
            ->leftJoin('rooms.promotions', 'promotions')
            ->leftJoin('rooms.galleries', 'roomGallery')
            ->leftJoin('rooms.beddings', 'beddings')
            ->leftJoin('rooms.taxes', 'taxes')
            ->leftJoin('promotions.action', 'action')
            ->leftJoin('h.favorites', 'favorites')
            //->leftJoin('h.reviews', 'reviews')
            ->leftJoin('h.cancellationPolicy', 'cancellationPolicy')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('location')
            ->addSelect('equipments')
            ->addSelect('equipmentGroup')
            ->addSelect('galleries')
            ->addSelect('roomEquipment')
            ->addSelect('roomEquipmentGroup')
            ->addSelect('promotions')
            ->addSelect('roomGallery')
            ->addSelect('beddings')
            ->addSelect('taxes')
            ->addSelect('action')
            ->addSelect('favorites')
            //->addSelect('reviews')
            ->addSelect('cancellationPolicy')
            ->where('h.enabled = 1')
            ->andWhere('h.closed = 0')
            ->where('h.slug = :slug')
            ->andWhere($qb->expr()->gte('rooms.occupant', ':occupant'))
            ->setParameter('slug', $slug)
            ->setParameter('occupant', $occupant);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getNumberByCity(City $city): int
    {
        return $this->createQueryBuilder('h')
            ->select('count(h.id)')
            ->leftJoin('h.location', 'location')
            ->where('h.enabled = 1')
            ->andWhere('location.city = :city')
            ->setParameter('city', $city->getSlug())
            ->getQuery()
            ->enableResultCache('600')
            ->getSingleScalarResult();
    }

    public function getNumberByUser(User $user): int
    {
        return $this->createQueryBuilder('h')
            ->select('count(h.id)')
            ->where('h.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteForUser(User $user): void
    {
        $this->createQueryBuilder('h')
            ->where('h.owner = :user')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }

    private function addSortData(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[HostelSortDataHandler::SORT_INDEX]) {
            $property = array_key_first($data[HostelSortDataHandler::SORT_INDEX]);

            $qb->orderBy($property, $data[HostelSortDataHandler::SORT_INDEX][$property]);
        }

        return $qb;
    }

    private function addFilterData(QueryBuilder $qb, array $data)
    {
        $qb = $this->hasOccupantQueryBuilder($qb, $data);
        $qb = $this->containsNameQueryBuilder($qb, $data);
        $qb = $this->hasPriceBetweenQueryBuilder($qb, $data);
        $qb = $this->hasEquipmentQueryBuilder($qb, $data);
        $qb = $this->hasRoomEquipmentQueryBuilder($qb, $data);
        $qb = $this->hasCategoriesQueryBuilder($qb, $data);
        $qb = $this->hasStarNumberQueryBuilder($qb, $data);
        $qb = $this->averageRatingQueryBuilder($qb, $data);

        return $qb;
    }

    private function hasOccupantQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->occupantPropertyPrefix]) {
            $qb->andWhere($qb->expr()->gte('rooms.occupant', ':occupant'))->setParameter('occupant', $data[$this->occupantPropertyPrefix]);
        }

        return $qb;
    }

    private function containsNameQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->namePropertyPrefix]) {
            $qb->andWhere('h.name LIKE :name')->setParameter('name', '%' . $data[$this->namePropertyPrefix] . '%');
        }

        return $qb;
    }

    private function hasPriceBetweenQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        $minPrice = $this->getDataByKey($data, $this->priceNameResolver->resolveMinPriceName());
        $maxPrice = $this->getDataByKey($data, $this->priceNameResolver->resolveMaxPriceName());

        if ($minPrice) {
            $qb->andWhere($qb->expr()->gte('rooms.price', ':minPrice'))->setParameter('minPrice', (int)$minPrice);
        }

        if ($maxPrice) {
            $qb->andWhere($qb->expr()->lte('rooms.price', ':maxPrice'))->setParameter('maxPrice', (int)$maxPrice);
        }

        return $qb;
    }

    private function hasEquipmentQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        foreach ($data as $key => $value) {
            if (0 === strpos($key, $this->equipmentPropertyPrefix) && 0 < count($value)) {
                $qb->andWhere('equipments.id IN (:ids)')->setParameter('ids', $value);
            }
        }

        return $qb;
    }

    private function hasRoomEquipmentQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        foreach ($data as $key => $value) {
            if (0 === strpos($key, $this->roomEquipmentPropertyPrefix) && 0 < count($value)) {
                $qb->andWhere('roomEquipment.id IN (:ids)')->setParameter('ids', $value);
            }
        }

        return $qb;
    }

    private function hasCategoriesQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->categoryPropertyPrefix, $data) &&
            $data[$this->categoryPropertyPrefix]) {
            $qb->andWhere('h.category = :category')->setParameter('category', (int)$data[$this->categoryPropertyPrefix]);
        }

        return $qb;
    }

    private function hasStarNumberQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->starNumberPropertyPrefix]) {
            $qb->andWhere('h.starNumber IN (:ids)')->setParameter('ids', $data[$this->starNumberPropertyPrefix]);
        }

        return $qb;
    }

    private function averageRatingQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->averageRatingPropertyPrefix]) {
            $qb->andWhere('h.averageRating = :averageRating')->setParameter('averageRating', $data[$this->averageRatingPropertyPrefix]);
        }

        return $qb;
    }

    private function getDataByKey(array $data, string $key): ?string
    {
        return $data[$key] ?? null;
    }
}
