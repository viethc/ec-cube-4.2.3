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
     * Get by Customer
     * 
     * @param mixed $customerId
     * @return \Plugin\CustomerCoupon42\Entity\CustomerCouponOrder[]
     */
    public function findByCustomer($customerId)
    {
        return $this->findBy(["customer_id" => $customerId]);
    }

    /**
     * Find by Coupon ID
     * 
     * @param mixed $couponId
     * @return \Plugin\CustomerCoupon42\Entity\CustomerCouponOrder|null
     */
    public function findByCoupon($couponId)
    {
        return $this->findOneBy(["coupon_id" => $couponId]);
    }
}
