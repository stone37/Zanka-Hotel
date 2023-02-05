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
            ->andWhere($qb->expr()->isNull('b.cancelledAt'))
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

    public function getAllAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('hostel')
            ->addSelect('user')
            ->addSelect('room')
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
            ->andWhere('b.owner = :user')
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
            ->andWhere($qb->expr()->isNull('b.cancelledAt'))
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
            ->andWhere($qb->expr()->isNotNull('b.cancelledAt'))
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

    public function getRoomBookingTotalByPartnerNumber(User $user, DateTime $start, DateTime $end): ?int
    {
        $qb = $this->createQueryBuilder('b')
                    ->leftJoin('b.hostel', 'hostel');
        $query = $qb->select('SUM(b.roomNumber)')
            ->where('b.checkin <= :start AND b.checkout >= :end')
            ->andWhere('hostel.owner = :owner')
            ->orWhere('b.checkin >= :start AND b.checkout <= :end')
            ->orWhere('b.checkin >= :start AND b.checkout >= :end AND b.checkin <= :end')
            ->orWhere('b.checkin <= :start AND b.checkout <= :end AND b.checkout >= :start')
            ->setParameters(['owner' => $user, 'start'=> $start, 'end'  => $end]);

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    public function getNewNumber(User $user = null): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNull('b.confirmedAt'))
            ->andWhere($qb->expr()->isNull('b.cancelledAt'))
            ->andWhere('b.checkout >= :date')
            ->setParameter('date', new DateTime());

        if ($user) {
            $qb->leftJoin('b.hostel', 'hostel')
                ->andWhere('hostel.owner = :owner')
                ->setParameter('owner', $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getEnabled(int $id): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->leftJoin('hostel.location', 'location')
            ->leftJoin('room.galleries', 'galleries')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->addSelect('location')
            ->addSelect('galleries')
            ->where('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByUser(\App\Model\BookingSearch $search, User $user, $state = null)
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->leftJoin('b.commande', 'commande')
            ->leftJoin('commande.payment', 'payment')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->addSelect('commande')
            ->addSelect('payment')
            ->where('b.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($state === Booking::NEW) {
            $qb->andWhere($qb->expr()->isNull('b.confirmedAt'))
                ->andWhere('b.checkout >= :date')
                ->setParameter('date', new DateTime());
        } elseif ($state === Booking::CONFIRMED) {
            $qb->andWhere($qb->expr()->isNotNull('b.confirmedAt'))
                ->andWhere('b.checkout > :date')
                ->setParameter('date', new DateTime());
        }  elseif ($state === Booking::CANCELLED) {
            $qb->andWhere($qb->expr()->isNotNull('b.cancelledAt'));
        } else {
            $qb->andWhere($qb->expr()->isNotNull('b.confirmedAt'))
                ->andWhere('b.checkout < :date')
                ->setParameter('date', new DateTime());
        }

        if ($search->getCode()) {
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());
        }

        return $qb->getQuery()->getResult();
    }

    public function getByApi(User $user, $state = null)
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->leftJoin('room.equipments', 'roomEquipments')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->addSelect('roomEquipments')
            ->where('b.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($state === Booking::NEW) {
            $qb->andWhere($qb->expr()->isNull('b.confirmedAt'))
                ->andWhere($qb->expr()->isNull('b.cancelledAt'))
                ->andWhere('b.checkout >= :date')
                ->setParameter('date', new DateTime());
        } elseif ($state === Booking::CONFIRMED) {
            $qb->andWhere($qb->expr()->isNotNull('b.confirmedAt'))
                ->andWhere('b.checkout > :date')
                ->setParameter('date', new DateTime());
        }  elseif ($state === Booking::CANCELLED) {
            $qb->andWhere($qb->expr()->isNotNull('b.cancelledAt'));
        }

        return $qb->getQuery()->getResult();
    }

    public function availableForPeriod(Room $room, DateTime $start, DateTime $end): int
    {
        $qb = $this->createQueryBuilder('b');
        $query = $qb->select('SUM(b.roomNumber)')
            ->where('b.checkin <= :start AND b.checkout >= :end')
            ->orWhere('b.checkin >= :start AND b.checkout <= :end')
            ->orWhere('b.checkin >= :start AND b.checkout >= :end AND b.checkin <= :end')
            ->orWhere('b.checkin <= :start AND b.checkout <= :end AND b.checkout >= :start')
            ->andWhere('b.room = :room')
            ->setParameters(['start'=> $start, 'end'  => $end, 'room' => $room]);

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    public function getByUserAndHostel(User $user, Hostel $hostel): ?Booking
    {
        $qb = $this->createQueryBuilder('b');

        return $qb->where('b.owner = :owner')
            ->andWhere('b.hostel = :hostel')
            ->andWhere('b.status = :status')
            ->andWhere('b.checkout < :date')
            ->andWhere($qb->expr()->isNotNull('b.confirmedAt'))
            ->setParameter('owner', $user)
            ->setParameter('hostel', $hostel)
            ->setParameter('status', Booking::CONFIRMED)
            ->setParameter('date', new DateTime())
            ->orderBy('b.createdAt', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByReferenceAndHostel(Hostel $hostel, string $reference): ?Booking
    {
        $qb = $this->createQueryBuilder('b');

        return $qb
            ->where('b.reference = :reference')
            ->andWhere('b.hostel = :hostel')
            ->andWhere('b.status = :status')
            ->andWhere('b.checkout < :date')
            ->andWhere($qb->expr()->isNotNull('b.confirmedAt'))
            ->setParameter('reference', $reference)
            ->setParameter('hostel', $hostel)
            ->setParameter('status', Booking::CONFIRMED)
            ->setParameter('date', new DateTime())
            ->orderBy('b.createdAt', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByEmail(string $email)
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.hostel', 'hostel')
            ->leftJoin('b.owner', 'owner')
            ->leftJoin('b.room', 'room')
            ->leftJoin('b.commande', 'commande')
            ->leftJoin('b.cancelation', 'cancelation')
            ->leftJoin('commande.payment', 'payment')
            ->addSelect('hostel')
            ->addSelect('owner')
            ->addSelect('room')
            ->addSelect('commande')
            ->addSelect('payment')
            ->where('b.email = :email')
            ->setParameter('email', $email)
            ->orderBy('b.createdAt', 'desc');

        return $qb->getQuery()->getResult();
    }

    public function deleteForUser(User $user): void
    {
        $this->createQueryBuilder('b')
            ->where('b.owner = :user')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }
}
