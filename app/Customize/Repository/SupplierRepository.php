<?php

namespace Customize\Repository;

use Customize\Entity\Supplier;
use Eccube\Common\EccubeConfig;
use Eccube\Doctrine\Query\Queries;
use Eccube\Repository\AbstractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

class SupplierRepository extends AbstractRepository
{
    /**
     * @var Queries
     */
    protected $queries;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * SupplierRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param Queries $queries
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        RegistryInterface $registry,
        Queries $queries,
        EccubeConfig $eccubeConfig
    ) {
        parent::__construct($registry, Supplier::class);
        $this->queries = $queries;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * Find all suppliers ordered by name.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|array
     * 
     */
    public function findAllOrderedByName(): ArrayCollection|array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a supplier by email.
     *
     * @param string $email
     * @return \Customize\Entity\Supplier|null
     */
    public function findOneByEmail(string $email): ?Supplier
    {
        return $this->createQueryBuilder('s')
            ->where('s.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
