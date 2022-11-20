<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Hostel;
use App\Entity\Room;
use App\Entity\User;
use App\Model\Admin\BookingSearch;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function add(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
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

    public function getAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');

        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('user')
            ->addSelect('room')
            ->where($qb->expr()->isNull('b.confirmedAt'))
            ->andWhere('b.checkout >= :date')
            ->setParameter('date', new DateTime())
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getConfirmAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('user')
            ->addSelect('room')
            ->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout > :date')
            ->setParameter('date', new DateTime())
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getCancelAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('user')
            ->addSelect('room')
            ->where($qb->expr()->isNotNull('b.cancelledAt'))
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getArchiveAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('user')
            ->addSelect('room')
            ->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout < :date')
            ->setParameter('date', new DateTime())
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function  getAdminByHostel(Hostel $hostel, BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('user')
            ->addSelect('room')
            ->andWhere('b.hostel = :hostel')
            ->setParameter('hostel', $hostel)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getAdminByUser(User $user, BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('user')
            ->addSelect('room')
            ->andWhere('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getAdminByRoom(Room $room, BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('user')
            ->addSelect('room')
            ->andWhere('b.room = :room')
            ->setParameter('room', $room)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        return $qb;
    }

    public function getCancel()
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'desc');

        $qb->andWhere($qb->expr()->isNull('b.cancelledAt'))
            ->andWhere($qb->expr()->isNull('b.confirmedAt'))
            ->andWhere('b.checkin <= :date')
            ->setParameter('date', new DateTime());

        return $qb->getQuery()->getResult();
    }

    public function getByPartner(User $user, BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');

        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->where($qb->expr()->isNull('b.confirmedAt'))
            ->andWhere('b.checkout >= :date')
            ->andWhere('hostel.owner = :owner')
            ->setParameter('date', new DateTime())
            ->setParameter('owner', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getConfirmByPartner(User $user, BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout > :date')
            ->andWhere('hostel.owner = :owner')
            ->setParameter('date', new DateTime())
            ->setParameter('owner', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getCancelByPartner(User $user, BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->where($qb->expr()->isNotNull('b.cancelledAt'))
            ->andWhere('hostel.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getArchiveByPartner(User $user, BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout < :date')
            ->andWhere('hostel.owner = :owner')
            ->setParameter('date', new DateTime())
            ->setParameter('owner', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        if ($search->getRoom()) {
            $qb->andWhere('b.room = :room')->setParameter('room', $search->getRoom());
        }

        return $qb;
    }

    public function getPartnerByRoom(User $user, Room $room, BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->andWhere('b.room = :room')
            ->andWhere('hostel.owner = :owner')
            ->setParameter('room', $room)
            ->setParameter('owner', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getHostel()) {
            $qb->andWhere('b.hostel = :hostel')->setParameter('hostel', $search->getHostel());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        return $qb;
    }

    public function getConfirmedByUserNumber(User $user)
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.owner = :owner')
            ->andWhere('b.checkout > :date')
            ->setParameter('owner', $user)
            ->setParameter('date', new DateTime());


        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getConfirmNumber(User $user = null): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout > :date')
            ->setParameter('date', new DateTime());

        if ($user) {
            $qb->leftJoin('b.hostel', 'hostel')
                ->andWhere('hostel.owner = :owner')
                ->setParameter('owner', $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getCancelNumber(User $user = null): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where('b.status = :status')
            ->setParameter('status', Booking::CANCELLED);

        if ($user) {
            $qb->leftJoin('b.hostel', 'hostel')
                ->andWhere('hostel.owner = :owner')
                ->setParameter('owner', $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getArchiveNumber(User $user = null): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout < :date')
            ->setParameter('date', new DateTime());

        if ($user) {
            $qb->leftJoin('b.hostel', 'hostel')
                ->andWhere('hostel.owner = :owner')
                ->setParameter('owner', $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getRoomBookingTotalNumber(DateTime $start, DateTime $end): ?int
    {
        $qb = $this->createQueryBuilder('b');
        $query = $qb->select('SUM(b.roomNumber)')
            ->where('b.checkin <= :start AND b.checkout >= :end')
            ->orWhere('b.checkin >= :start AND b.checkout <= :end')
            ->orWhere('b.checkin >= :start AND b.checkout >= :end AND b.checkin <= :end')
            ->orWhere('b.checkin <= :start AND b.checkout <= :end AND b.checkout >= :start')
            ->setParameters(['start'=> $start, 'end'  => $end]);

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    public function getNewNumber(User $user = null): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNull('b.confirmedAt'))
            ->andWhere('b.checkout >= :date')
            ->setParameter('date', new DateTime());

        if ($user) {
            $qb->leftJoin('b.hostel', 'hostel')
                ->andWhere('hostel.owner = :owner')
                ->setParameter('owner', $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
