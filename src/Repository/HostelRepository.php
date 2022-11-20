<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Hostel;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\HostelSearch;
use App\Model\Admin\HostelAdminSearch;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hostel::class);
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
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('h.owner', 'owner')
            ->leftJoin('h.bookings', 'bookings')
            ->leftJoin('h.reviews', 'reviews')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('equipments')
            ->addSelect('owner')
            ->addSelect('bookings')
            ->addSelect('reviews')
            ->where('h.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('h.position', 'asc');

        return $qb;
    }

    public function getTotalEnabled(HostelSearch $search)
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.rooms', 'rooms')
            ->leftJoin('h.category', 'category')
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('h.owner', 'user')
            ->leftJoin('h.bookings', 'bookings')
            ->leftJoin('rooms.equipments', 're')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('equipments')
            ->addSelect('user')
            ->addSelect('bookings')
            ->addSelect('re')
            ->orderBy('h.position', 'asc');

        if ($search->getName()) {
            $qb->andWhere('h.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        if ($search->getCategory()) {
            $qb->andWhere('h.category = :category')->setParameter('category', $search->getCategory());
        }

        if ($search->getStar()) {
            $qb->andWhere('h.starNumber = :star')->setParameter('star', $search->getStar());
        }

        /*if ($search->getOffer()) {
            $qb->andWhere($qb->expr()->isNotNull('rooms.promotion'));
        }*/

        if ($search->getPriceMin()) {
            $qb->andWhere('rooms.price >= :priceMin')->setParameter('priceMin', (int) $search->getPriceMin());
        }

        if ($search->getPriceMax()) {
            $qb->andWhere('rooms.price <= :priceMax')->setParameter('priceMax', (int) $search->getPriceMax());
        }

        /*if ($search->getZone())
            $qb->andWhere('h.zone = :zone')->setParameter('zone', $search->getZone());*/

        /*if ($search->getRating()) {
            $qb->andWhere('h.averageRating = :rating')->setParameter('rating', (int)$search->getRating());
        }

        if ($search->getEquipment())
            $qb->andWhere('equipments.name = :equipment')->setParameter('equipment', $search->getEquipment());

        if ($search->getRoomEquipment())
            $qb->andWhere('re.name = :re')->setParameter('re', $search->getRoomEquipment());*/

        //$qb = $this->searchByDataSort($qb, $search);

        return $qb;
    }

    private function searchByDataSort(QueryBuilder $qb, HostelSearch $search): QueryBuilder
    {
        if ($search->getOrder()) {
            if ($search->getOrder() == 'priceAsc') {
                $qb->orderBy('rooms.price', 'asc');
            } elseif ($search->getOrder() == 'priceDesc') {
                $qb->orderBy('rooms.price', 'desc');
            } elseif ($search->getOrder() == 'starAsc') {
                $qb->orderBy('h.starNumber', 'asc');
            } elseif ($search->getOrder() == 'starDesc') {
                $qb->orderBy('h.starNumber', 'desc');
            } else {
                $qb->orderBy('h.averageRating', 'desc');
            }
        } else {
            $qb->orderBy('h.averageRating', 'desc');
        }

        return $qb;
    }

    public function getBySlug(string $slug): ?Hostel
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.rooms', 'rooms')
            ->leftJoin('h.category', 'category')
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('h.owner', 'user')
            ->leftJoin('h.bookings', 'bookings')
            ->leftJoin('rooms.equipments', 're')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('equipments')
            ->addSelect('user')
            ->addSelect('bookings')
            ->addSelect('re')
            ->where('h.slug = :slug')
            ->setParameter('slug', $slug);

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
            ->setParameter('city', $city)
            ->getQuery()
            ->enableResultCache('600')
            ->getSingleScalarResult();
    }
}
