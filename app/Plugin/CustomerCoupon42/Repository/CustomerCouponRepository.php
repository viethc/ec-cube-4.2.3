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
     * Save
     * 
     * @param \Plugin\CustomerCoupon42\Entity\CustomerCoupon $CustomerCoupon
     * @return void
     */
    public function save($CustomerCoupon)
    {
        $em = $this->getEntityManager();
        $em->persist($CustomerCoupon);
        $em->flush();
    }

    /**
     * クーポン情報を有効/無効にする.
     *
     * @param CustomerCoupon $Coupon
     *
     * @return bool
     */
    public function enableCoupon(CustomerCoupon $CustomerCoupon)
    {
        $em = $this->getEntityManager();

        // クーポン情報を書き換える
        $CustomerCoupon->setEnableFlag(!$CustomerCoupon->getEnableFlag());

        // クーポン情報を登録する
        $em->persist($CustomerCoupon);
        $em->flush();

        return true;
    }

    /**
     * クーポン情報を削除する.
     *
     * @param CustomerCoupon $CustomerCoupon
     *
     * @return bool
     */
    public function deleteCoupon(CustomerCoupon $CustomerCoupon)
    {
        $em = $this->getEntityManager();

        // クーポン情報を書き換える
        $CustomerCoupon->setVisible(false);

        // クーポン情報を登録する
        $em->persist($CustomerCoupon);
        $em->flush();

        return true;
    }

    /**
     * Get danh sách Customer Coupon còn hiệu lực
     */
    public function findByActiveCoupons()
    {
        $qb = $this->createQueryBuilder('c')->select('c')->Where('c.visible = true');

        $qb->andWhere('c.enable_flag = :enable_flag')->setParameter('enable_flag', Constant::ENABLED);
        $qb->andWhere('c.coupon_use_time > 0');
        $qb->orderBy('c.coupon_lower_limit');

        return $qb->getQuery()->getResult();
    }

    /**
     * Summary of findOneUseCoupon
     * @param mixed $totalPrice
     */
    public function findOneUseCoupon($totalPrice = 0)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->Where('c.visible = true');
        $qb->andWhere('c.enable_flag = :enable_flag')
            ->setParameter('enable_flag', Constant::ENABLED);
        $qb->andWhere('c.coupon_use_time > 0');

        if ($totalPrice > 0) {
            $qb->andWhere('c.coupon_lower_limit <= :total_price')
                ->setParameter('total_price', $totalPrice);
        }
        // SORT BY
        $qb->orderBy('c.coupon_lower_limit', 'DESC');

        // LIMIT 1
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
