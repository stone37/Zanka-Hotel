<?php

namespace App\Repository;

use App\Entity\RoomEquipmentGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoomEquipmentGroup>
 *
 * @method RoomEquipmentGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomEquipmentGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomEquipmentGroup[]    findAll()
 * @method RoomEquipmentGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomEquipmentGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomEquipmentGroup::class);
    }

    public function add(RoomEquipmentGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RoomEquipmentGroup $entity, bool $flush = false): void
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
