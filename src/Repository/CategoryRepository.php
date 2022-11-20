<?php

namespace App\Repository;

use App\Entity\Category;
use App\Model\Admin\CategorySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function add(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
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

    public function getEnabledData(): ?array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.position', 'asc')
            ->andWhere('c.enabled = 1');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['name']] = $result['id'];
        }

        return $data;
    }

    public function getEnabled(): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.position', 'asc')
            ->andWhere('c.enabled = 1');

        return $qb;
    }

    public function getAdmins(CategorySearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.position', 'asc');

        if ($search->isEnabled()) {
            $qb->andWhere('c.enabled = 1');
        }

        if ($search->getName()) {
            $qb->andWhere('c.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        return $qb;
    }

    public function getHomeEnabled()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.hostels', 'hostels')
            ->addSelect('hostels')
            ->where('c.enabled = 1')
            ->orderBy('c.position', 'asc')
            ->getQuery()
            ->getResult();
    }



}
