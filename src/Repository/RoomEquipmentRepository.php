<?php

namespace App\Repository;

use App\Entity\RoomEquipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoomEquipment>
 *
 * @method RoomEquipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomEquipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomEquipment[]    findAll()
 * @method RoomEquipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomEquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomEquipment::class);
    }

    public function add(RoomEquipment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RoomEquipment $entity, bool $flush = false): void
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
