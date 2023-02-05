<?php

namespace App\Repository;

use App\Entity\CancelPayout;
use App\Model\Admin\PayoutSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CancelPayout>
 *
 * @method CancelPayout|null find($id, $lockMode = null, $lockVersion = null)
 * @method CancelPayout|null findOneBy(array $criteria, array $orderBy = null)
 * @method CancelPayout[]    findAll()
 * @method CancelPayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CancelPayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CancelPayout::class);
    }

    public function add(CancelPayout $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CancelPayout $entity, bool $flush = false): void
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

    public function getAdmins(PayoutSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('cp');

        $qb->leftJoin('cp.commande', 'commande')
            ->leftJoin('cp.owner', 'user')
            ->leftJoin('commande.hostel', 'hostel')
            ->addSelect('commande')
            ->addSelect('user')
            ->addSelect('hostel')
            ->orderBy('cp.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('commande.hostel = :hostel')->setParameter('hostel', (int) $search->getHostel());
        }

        return $qb;
    }

    public function getMonthlyReport(int $year)
    {
        return $this->createQueryBuilder('cp')
            ->select(
                'EXTRACT(MONTH FROM cp.createdAt) as month',
                'ROUND(SUM(cp.amount) * 100) / 100 as amount'
            )
            ->groupBy('month')
            ->where('cp.state = :state')
            ->andWhere('EXTRACT(YEAR FROM cp.createdAt) = :year')
            ->setParameter('state', CancelPayout::PAYOUT_COMPLETED)
            ->setParameter('year', $year)
            ->orderBy('month', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
