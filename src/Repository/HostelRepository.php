<?php

namespace App\Repository;

use App\Entity\Hostel;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\Admin\HostelSearch;

/**
 * @extends ServiceEntityRepository<Hostel>
 *
 * @method Hostel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hostel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hostel[]    findAll()
 * @method Hostel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HostelRepository extends ServiceEntityRepository
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

    public function getEnabledData(): ?array
    {
        $qb = $this->createQueryBuilder('h')
            ->orderBy('h.name', 'asc')
            ->andWhere('h.enabled = 1');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['name']] = $result['id'];
        }

        return $data;
    }

    public function getPartnerEnabledData(User $user): ?array
    {
        $qb = $this->createQueryBuilder('h')
                ->where('h.user = :user')
                ->setParameter('user', $user)
                ->orderBy('h.name', 'asc');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['name']] = $result['id'];
        }

        return $data;
    }

    public function getAdmins(HostelSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.rooms', 'rooms')
            ->leftJoin('h.category', 'category')
            ->leftJoin('h.equipments', 'equipments')
            ->leftJoin('h.user', 'user')
            ->leftJoin('h.bookings', 'bookings')
            ->addSelect('rooms')
            ->addSelect('category')
            ->addSelect('equipments')
            ->addSelect('user')
            ->addSelect('bookings')
            ->orderBy('h.name', 'asc');

        if ($search->isEnabled()) {
            $qb->andWhere('h.enabled = 1');
        }

        if ($search->getName()) {
            $qb->andWhere('h.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        if ($search->getCategory()) {
            $qb->andWhere('h.category = :category')->setParameter('category', (int)$search->getCategory());
        }

        if ($search->getEmail()) {
            $qb->andWhere('h.email = :email')->setParameter('email', (int)$search->getEmail());
        }

        return $qb;
    }

    public function getByPartner(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('h')
            ->where('h.user = :user')
            ->setParameter('user', $user)
            ->orderBy('h.name', 'asc');

        return $qb;
    }
}
