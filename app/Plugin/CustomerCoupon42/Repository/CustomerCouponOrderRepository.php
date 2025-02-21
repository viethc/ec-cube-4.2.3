<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomerCoupon42\Repository;

use Eccube\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;
use Eccube\Entity\Customer;
use Plugin\CustomerCoupon42\Entity\CustomerCouponOrder;

/**
 * CustomerCouponOrderRepository.
 *
 */
class CustomerCouponOrderRepository extends AbstractRepository
{
    /**
     * CustomerCouponOrderRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerCouponOrder::class);
    }

    /**
     * Save
     * 
     * @param \Plugin\CustomerCoupon42\Entity\CustomerCouponOrder $CustomerCouponOrder
     * @return void
     */
    public function save($CustomerCouponOrder)
    {
        $em = $this->getEntityManager();
        $em->persist($CustomerCouponOrder);
        $em->flush();
    }

    /**
     * クーポン受注情報を取得する.
     *
     * @param string $preOrderId
     *
     * @return \Plugin\CustomerCoupon42\Entity\CustomerCouponOrder
     */
    public function getCouponOrder($preOrderId)
    {
        $CustomerCouponOrder = $this->findOneBy([
            'pre_use_order_id' => $preOrderId,
        ]);

        return $CustomerCouponOrder;
    }

    /**
     * Find `CustomerCouponOrder` was lost session
     */
    public function findByLostSession()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->Where('c.visible = true')
            ->andWhere('c.use_order_id > 0')
            ->andWhere('c.pre_use_order_id IS NOT NULL')
            ->andWhere('c.date_of_use IS NULL');

        // LIMIT 1
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Get list CustomerCouponOrder by Customer
     * 
     * @param \Eccube\Entity\Customer $Customer
     * @return \Plugin\CustomerCoupon42\Entity\CustomerCouponOrder[]
     */
    public function getOptionsByCustomer($Customer)
    {
        $customerId = $Customer->getId();
        $customerEmail = $Customer->getEmail();
        $currenDateTime = new \DateTime();
        $currenDateTime->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->Where('c.visible = true');

        $qb->andWhere('c.customer_id = :customer_id OR c.customer_email = :customer_email')
            ->setParameter('customer_id', $customerId)
            ->setParameter('customer_email', $customerEmail);

        $qb->andWhere('c.available_from_date <= :cur_date_time OR c.available_from_date IS NULL')
            ->setParameter('cur_date_time', $currenDateTime);

        $qb->andWhere(':cur_date_time <= c.available_to_date OR c.available_to_date IS NULL')
            ->setParameter('cur_date_time', $currenDateTime);

        $qb->andWhere('c.date_of_use IS NULL');
        $qb->orderBy('c.discount_rate');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find by Customer, Coupon ID
     * 
     * @param \Eccube\Entity\Customer $Customer
     * @param mixed $couponId
     * @return \Plugin\CustomerCoupon42\Entity\CustomerCouponOrder|null
     */
    public function findByCustomerCoupon($Customer, $couponId)
    {
        $customerId = $Customer->getId();
        $customerEmail = $Customer->getEmail();
        $currenDateTime = new \DateTime();
        $currenDateTime->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->Where('c.visible = true');

        $qb->andWhere('c.coupon_id = :coupon_id')
            ->setParameter('coupon_id', $couponId);
        
        $qb->andWhere('c.customer_id = :customer_id OR c.customer_email = :customer_email')
            ->setParameter('customer_id', $customerId)
            ->setParameter('customer_email', $customerEmail);

        $qb->andWhere('c.available_from_date <= :cur_date_time OR c.available_from_date IS NULL')
            ->setParameter('cur_date_time', $currenDateTime);

        $qb->andWhere(':cur_date_time <= c.available_to_date OR c.available_to_date IS NULL')
            ->setParameter('cur_date_time', $currenDateTime);

        $qb->andWhere('c.date_of_use IS NULL');
        $qb->orderBy('c.available_to_date');

            // LIMIT 1
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
