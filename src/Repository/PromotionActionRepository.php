<?php

namespace App\Repository;

use App\Entity\PromotionAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PromotionAction>
 *
 * @method PromotionAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromotionAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromotionAction[]    findAll()
 * @method PromotionAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromotionAction::class);
    }

    public function add(PromotionAction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PromotionAction $entity, bool $flush = false): void
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
