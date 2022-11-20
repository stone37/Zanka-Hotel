<?php

namespace App\Repository;

use App\Entity\HostelGallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gallery>
 *
 * @method HostelGallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method HostelGallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method HostelGallery[]    findAll()
 * @method HostelGallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HostelGalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HostelGallery::class);
    }

    public function add(HostelGallery $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HostelGallery $entity, bool $flush = false): void
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

    public function getGalleries(int $limit = 3)
    {
        $qb = $this->createQueryBuilder('g')->select('COUNT(g)');

        $totalRecords = $qb->getQuery()->getSingleScalarResult();

        if ($totalRecords < 1) {
            return null;
        }

        if ($totalRecords < $limit) {
            return $qb->select('g')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        } else {
            return $qb->select('g')
                ->setMaxResults($limit)
                ->setFirstResult(rand(0, $totalRecords - $limit))
                ->getQuery()
                ->getResult();
        }
    }
}
