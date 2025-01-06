<?php

namespace Customize\Repository;

use DateTime;
use Eccube\Entity\Master\Pref;
use Eccube\Common\EccubeConfig;
use Eccube\Doctrine\Query\Queries;
use Customize\Entity\RegionalDiscount;
use Eccube\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

class RegionalDiscountRepository extends AbstractRepository
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
        parent::__construct($registry, RegionalDiscount::class);
        $this->queries = $queries;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * Get the RegionalDiscount with the highest discount amount and valid end date.
     *
     * @param \Eccube\Entity\Master\Pref $pref
     * 
     * @return \Customize\Entity\RegionalDiscount|null
     * 
     */
    public function findHighestDiscount(Pref $pref): RegionalDiscount | null
    {
        $queryBuilder = $this->createQueryBuilder('rd');

        $queryBuilder->where('rd.end_date > :now')
            ->andWhere('rd.Pref = :pref')
            ->setParameter('now', new DateTime())
            ->setParameter('pref', $pref)
            ->orderBy('rd.amount', 'DESC');

        return $queryBuilder->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
