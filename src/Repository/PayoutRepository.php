<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Payout;
use App\Entity\User;
use App\Model\Admin\PayoutSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payouts>
 *
 * @method Payout|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payout|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payout[]    findAll()
 * @method Payout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payout::class);
    }

    public function add(Payout $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payout $entity, bool $flush = false): void
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
        $qb = $this->createQueryBuilder('p');

        $qb->leftJoin('p.commande', 'commande')
            ->leftJoin('p.owner', 'user')
            ->leftJoin('commande.hostel', 'hostel')
            ->addSelect('commande')
            ->addSelect('user')
            ->addSelect('hostel')
            ->orderBy('p.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('commande.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        return $qb;
    }

    public function getByPartner(PayoutSearch $search, User $user): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        $qb->leftJoin('p.commande', 'commande')
            ->leftJoin('p.owner', 'user')
            ->leftJoin('commande.hostel', 'hostel')
            ->leftJoin('commande.booking', 'booking')
            ->leftJoin('commande.payment', 'payment')
            ->addSelect('commande')
            ->addSelect('user')
            ->addSelect('hostel')
            ->addSelect('booking')
            ->addSelect('payment')
            ->where('p.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('p.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('commande.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        return $qb;
    }

    public function getLasts()
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'desc')
            ->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }

    public function getMonthlyRevenues(): array
    {
        return $this->aggregateRevenus('%Y-%m', '%m', 24);
    }

    public function getDailyRevenues(): array
    {
        return $this->aggregateRevenus('%Y-%m-%d', '%d', 30);
    }

    public function getMonthlyReport(int $year): array
    {
        return $this->createQueryBuilder('p')
            ->select(
                'EXTRACT(MONTH FROM p.createdAt) as month',
                'ROUND(SUM(p.amount) * 100) / 100 as amount'
            )
            ->groupBy('month')
            ->where('p.state = :state')
            ->andWhere('EXTRACT(YEAR FROM p.createdAt) = :year')
            ->setParameter('state', Payout::PAYOUT_COMPLETED)
            ->setParameter('year', $year)
            ->orderBy('month', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyReportByPartner(User $user, int $year): array
    {
        return $this->createQueryBuilder('p')
            ->select(
                'EXTRACT(MONTH FROM p.createdAt) as month',
                'ROUND(SUM(p.amount) * 100) / 100 as amount'
            )
            ->groupBy('month')
            ->where('p.state = :state')
            ->andWhere('p.owner = :user')
            ->andWhere('EXTRACT(YEAR FROM p.createdAt) = :year')
            ->orderBy('month', 'DESC')
            ->setParameter('state', Payout::PAYOUT_COMPLETED)
            ->setParameter('year', $year)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getSentNumber(): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.state = :state')
            ->setParameter('state', Payout::PAYOUT_COMPLETED);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getNewNumber(): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.state = :state')
            ->setParameter('state', Payout::PAYOUT_NEW);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getCancelNumber()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.state = :state')
            ->setParameter('state', Payout::PAYOUT_CANCEL);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function totalSent(User $user = null): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('ROUND(SUM(p.amount)) as amount')
            ->where('p.state = :state')
            ->setParameter('state', Payout::PAYOUT_COMPLETED);

        if ($user) {
            $qb = $this->hasPartner($qb, $user);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function totalCancel(User $user = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('ROUND(SUM(p.amount)) as amount')
            ->where('p.state = :state')
            ->getQuery()
            ->getSingleScalarResult()
            ->setParameter('state', Payout::PAYOUT_CANCEL);

        if ($user) {
            $qb = $this->hasPartner($qb, $user);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function aggregateRevenus(string $group, string $label, int $limit): array
    {
        return array_reverse($this->createQueryBuilder('p')
            ->select(
                "DATE_FORMAT(p.createdAt, '$label') as date",
                "DATE_FORMAT(p.createdAt, '$group') as fulldate",
                'ROUND(SUM(p.amount)) as amount'
            )
            ->where('p.state = :state')
            ->groupBy('fulldate', 'date')
            ->orderBy('fulldate', 'DESC')
            ->setParameter('state', Payout::PAYOUT_COMPLETED)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    private function hasPartner(QueryBuilder $qb, User $user): QueryBuilder
    {
        return $qb->leftJoin('p.commande', 'commande')
            ->leftJoin('commande.hostel', 'hostel')
            ->andWhere('hostel.owner = :owner')
            ->setParameter('owner', $user);
    }
}
