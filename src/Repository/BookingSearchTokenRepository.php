<?php

namespace App\Repository;

use App\Entity\BookingSearchToken;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookingSearchToken>
 *
 * @method BookingSearchToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingSearchToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingSearchToken]    findAll()
 * @method BookingSearchToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingSearchTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingSearchToken::class);
    }

    public function add(BookingSearchToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BookingSearchToken $entity, bool $flush = false): void
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

    /**
     * Supprime les anciennes demande de verification de reservation.
     */
    public function clean(): int
    {
        return $this->createQueryBuilder('p')
            ->where('p.createdAt < :date')
            ->setParameter('date', new DateTime('-1 day'))
            ->delete(BookingSearchToken::class, 'p')
            ->getQuery()
            ->execute();
    }
}
