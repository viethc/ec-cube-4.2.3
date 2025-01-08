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

use Eccube\Common\Constant;
use Eccube\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;

/**
 * CustomerCouponRepository.
 *
 */
class CustomerCouponRepository extends AbstractRepository
{
    /**
     * CustomerCouponRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerCoupon::class);
    }

    /**
     * クーポン情報を有効/無効にする.
     *
     * @param CustomerCoupon $Coupon
     *
     * @return bool
     */
    public function enableCoupon(CustomerCoupon $Coupon)
    {
        $em = $this->getEntityManager();

        // クーポン情報を書き換える
        $Coupon->setEnableFlag(!$Coupon->getEnableFlag());

        // クーポン情報を登録する
        $em->persist($Coupon);
        $em->flush($Coupon);

        return true;
    }

    /**
     * クーポン情報を削除する.
     *
     * @param CustomerCoupon $Coupon
     *
     * @return bool
     */
    public function deleteCoupon(CustomerCoupon $Coupon)
    {
        $em = $this->getEntityManager();

        // クーポン情報を書き換える
        $Coupon->setVisible(false);

        // クーポン情報を登録する
        $em->persist($Coupon);
        $em->flush($Coupon);

        return true;
    }
}
