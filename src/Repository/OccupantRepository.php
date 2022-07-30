<?php

namespace App\Repository;

use App\Entity\Occupant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Occupant>
 *
 * @method Occupant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Occupant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Occupant[]    findAll()
 * @method Occupant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OccupantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Occupant::class);
    }

    public function add(Occupant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Occupant $entity, bool $flush = false): void
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


}
