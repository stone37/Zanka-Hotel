<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function add(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payment $entity, bool $flush = false): void
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

    public function getLasts(User $user = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'desc')
            ->setMaxResults(5);

        if ($user) {
            $qb->leftJoin('p.commande', 'commande')
                ->leftJoin('commande.hostel', 'hostel')
                ->addSelect('commande')
                ->addSelect('hostel')
                ->andWhere('hostel.owner = :owner')
                ->setParameter('owner', $user);
        }

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

    public function getMonthlyPartnerRevenues(User $user): array
    {
        return $this->aggregatePartnerRevenus($user,'%Y-%m', '%m', 24);
    }

    public function getDailyPartnerRevenues(User $user): array
    {
        return $this->aggregatePartnerRevenus($user,'%Y-%m-%d', '%d', 30);
    }

    public function getMonthlyTaxRevenues(): array
    {
        return $this->aggregateTaxRevenus('%Y-%m', '%m', 24);
    }

    public function getDailyTaxRevenues(): array
    {
        return $this->aggregateTaxRevenus('%Y-%m-%d', '%d', 30);
    }

    public function getMonthlyReport(int $year): array
    {
        return $this->createQueryBuilder('p')
            ->select(
                'EXTRACT(MONTH FROM p.createdAt) as month',
                'ROUND(SUM(p.price) * 100) / 100 as price',
                'ROUND(SUM(p.taxe) * 100) / 100 as tax',
                'ROUND(SUM(p.discount) * 100) / 100 as fee'
            )
            ->groupBy('month')
            ->where('p.refunded = false')
            ->andWhere('EXTRACT(YEAR FROM p.createdAt) = :year')
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
                'ROUND(SUM(p.price) * 100) / 100 as price',
                'ROUND(SUM(p.taxe) * 100) / 100 as tax',
                'ROUND(SUM(p.discount) * 100) / 100 as fee'
            )
            ->leftJoin('p.commande', 'commande')
            ->leftJoin('commande.hostel', 'hostel')
            ->groupBy('month')
            ->where('p.refunded = false')
            ->andWhere('EXTRACT(YEAR FROM p.createdAt) = :year')
            ->andWhere('hostel.owner = :owner')
            ->setParameter('year', $year)
            ->orderBy('month', 'DESC')
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult();
    }

    public function getNumber(User $user = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)');

        if ($user) {
            $qb = $this->hasPartner($qb, $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function totalRevenues(User $user = null)
    {
        $qb =  $this->createQueryBuilder('p')
            ->select('ROUND(SUM(p.price)) as amount')
            ->where('p.refunded = false');

        if ($user) {
            $qb = $this->hasPartner($qb, $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function totalTax(User $user = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('ROUND(SUM(p.taxe)) as taxe')
            ->where('p.refunded = false');

        if ($user) {
            $qb = $this->hasPartner($qb, $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function totalReduction(User $user = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('ROUND(SUM(p.discount)) as discount')
            ->where('p.refunded = false');

        if ($user) {
            $qb = $this->hasPartner($qb, $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function aggregateRevenus(string $group, string $label, int $limit): array
    {
        return array_reverse($this->createQueryBuilder('p')
            ->select(
                "DATE_FORMAT(p.createdAt, '$label') as date",
                "DATE_FORMAT(p.createdAt, '$group') as fulldate",
                'ROUND(SUM(p.price)) as amount'
            )
            ->where('p.refunded = false')
            ->groupBy('fulldate', 'date')
            ->orderBy('fulldate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    private function aggregatePartnerRevenus(User $user, string $group, string $label, int $limit): array
    {
        return array_reverse($this->createQueryBuilder('p')
            ->select(
                "DATE_FORMAT(p.createdAt, '$label') as date",
                "DATE_FORMAT(p.createdAt, '$group') as fulldate",
                'ROUND(SUM(p.price)) as amount'
            )
            ->leftJoin('p.commande', 'commande')
            ->leftJoin('commande.hostel', 'hostel')
            ->where('p.refunded = false')
            ->andWhere('hostel.owner = :owner')
            ->groupBy('fulldate', 'date')
            ->orderBy('fulldate', 'DESC')
            ->setParameter('owner', $user)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    private function aggregateTaxRevenus(string $group, string $label, int $limit): array
    {
        return array_reverse($this->createQueryBuilder('p')
            ->select(
                "DATE_FORMAT(p.createdAt, '$label') as date",
                "DATE_FORMAT(p.createdAt, '$group') as fulldate",
                'ROUND(SUM(p.taxe)) as amount'
            )
            ->where('p.refunded = false')
            ->groupBy('fulldate', 'date')
            ->orderBy('fulldate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    public function findFor(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.commande', 'commande')
            ->leftJoin('commande.booking', 'booking')
            ->leftJoin('booking.room', 'room')
            ->addSelect('commande')
            ->addSelect('booking')
            ->addSelect('room')
            ->where('commande.user = :user')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findForId(int $id, User|UserInterface $user)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.commande', 'commande')
            ->leftJoin('commande.booking', 'booking')
            ->leftJoin('booking.room', 'room')
            ->addSelect('commande')
            ->addSelect('booking')
            ->addSelect('room')
            ->where('commande.user = :user')
            ->andWhere('p.id = :id')
            ->setParameter('user', $user)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function hasPartner(QueryBuilder $qb, User $user): QueryBuilder
    {
        return $qb->leftJoin('p.commande', 'commande')
            ->leftJoin('commande.hostel', 'hostel')
            ->andWhere('hostel.owner = :owner')
            ->setParameter('owner', $user);
    }
}
