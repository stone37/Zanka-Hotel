<?php

namespace App\Api\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

class UserQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if (User::class !== $resourceClass) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.confirmationToken IS NULL', $rootAlias))
            ->andWhere(sprintf('%s.roles LIKE :roles', $rootAlias))
            ->andWhere(sprintf('%s.roles NOT LIKE :roleP', $rootAlias))
            ->andWhere(sprintf('%s.roles NOT LIKE :roleA', $rootAlias))
            ->andWhere(sprintf('%s.roles NOT LIKE :roleSA', $rootAlias))
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleP', '%'."ROLE_PARTNER".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%');
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        if (User::class !== $resourceClass) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.confirmationToken IS NULL', $rootAlias))
            ->andWhere(sprintf('%s.roles LIKE :roles', $rootAlias))
            ->andWhere(sprintf('%s.roles NOT LIKE :roleP', $rootAlias))
            ->andWhere(sprintf('%s.roles NOT LIKE :roleA', $rootAlias))
            ->andWhere(sprintf('%s.roles NOT LIKE :roleSA', $rootAlias))
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleP', '%'."ROLE_PARTNER".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%');
    }
}
