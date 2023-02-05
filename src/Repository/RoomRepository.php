<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\User;
use App\Model\Admin\RoomSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Room>
 *
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function add(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Room $entity, bool $flush = false): void
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

    public function getWithFilter()
    {
        $results = $this->createQueryBuilder('r')
            ->andWhere('r.enabled = 1')
            ->orderBy('r.position', 'asc')
            ->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['name']] = $result['id'];
        }

        return $data;
    }

    public function getWithPartnerFilter(User|UserInterface $user)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->addSelect('hostel')
            ->where('hostel.owner = :user')
            ->andWhere('r.enabled = 1')
            ->orderBy('r.position', 'asc')
            ->setParameter('user', $user)
            ->getQuery()->getResult();
    }

    public function getAdmins(RoomSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.taxes', 'taxes')
            ->addSelect('hostel')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('taxes')
            ->orderBy('r.position', 'asc');

        if  ($search->getHostel()) {
            $qb->andWhere('r.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->isEnabled()) {
            $qb->andWhere('r.enabled = 1');
        }

        if ($search->getName()) {
            $qb->andWhere('r.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        return $qb;
    }

    public function getByPartner(User $user, RoomSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.galleries', 'galleries')
            ->addSelect('hostel')
            ->addSelect('bookings')
            ->addSelect('galleries')
            ->where('hostel.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('r.position', 'asc');


        if ($search->isEnabled()) {
            $qb->andWhere('r.enabled = 1');
        }

        if ($search->getName()) {
            $qb->andWhere('r.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        if ($search->getHostel()) {
            $qb->andWhere('r.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        return $qb;
    }

    public function getByPartnerQueryBuilder(User|UserInterface $user): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->addSelect('hostel')
            ->where('hostel.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('r.position', 'asc');

        return $qb;
    }

    public function getRoomTotalNumber(): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.roomNumber)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getRoomByPartnerTotalNumber(User $user): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.roomNumber)')
            ->leftJoin('r.hostel', 'hostel')
            ->where('hostel.owner = :owner')
            ->setParameter('owner', $user);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getRoomEnabledTotalNumber(): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.roomNumber)')
            ->where('r.enabled = 1');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getRoomEnabledByPartnerTotalNumber(User $user): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.roomNumber)')
            ->leftJoin('r.hostel', 'hostel')
            ->where('r.enabled = 1')
            ->andWhere('hostel.owner = :owner')
            ->setParameter('owner', $user);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getEnabled(int $id): ?Room
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->leftJoin('hostel.location', 'location')
            ->leftJoin('hostel.cancellationPolicy', 'cancellationPolicy')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.taxes', 'taxes')
            ->addSelect('hostel')
            ->addSelect('location')
            ->addSelect('cancellationPolicy')
            ->addSelect('galleries')
            ->addSelect('equipments')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('taxes')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
