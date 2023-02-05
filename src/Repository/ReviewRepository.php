<?php

namespace App\Repository;

use App\Entity\Hostel;
use App\Entity\Review;
use App\Entity\User;
use App\Model\Admin\ReviewSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function add(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Review $entity, bool $flush = false): void
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

    public function getAdmins(ReviewSearch $search)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->leftJoin('r.owner', 'user')
            ->addSelect('hostel')
            ->addSelect('user')
            ->orderBy('r.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('r.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        return $qb;
    }

    public function getByUser(User $user)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->leftJoin('r.owner', 'user')
            ->addSelect('hostel')
            ->addSelect('user')
            ->where('r.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'desc');

        return $qb;
    }

    public function getByPartner(ReviewSearch $search, User $user)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->leftJoin('r.owner', 'owner')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->where('hostel.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('r.createdAt', 'desc');


        if ($search->getHostel()) {
            $qb->andWhere('r.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        return $qb;
    }

    public function getByHostel(Hostel $hostel)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.hostel', 'hostel')
            ->leftJoin('r.owner', 'owner')
            ->leftJoin('r.booking', 'booking')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('booking')
            ->where('r.hostel = :hostel')
            ->andWhere('r.enabled = 1')
            ->setParameter('hostel', $hostel)
            ->orderBy('r.rating', 'desc');

        return $qb->getQuery()->getResult();
    }
}
