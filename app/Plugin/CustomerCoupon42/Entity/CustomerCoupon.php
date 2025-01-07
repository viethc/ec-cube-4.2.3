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

namespace Plugin\CustomerCoupon42\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Coupon
 *
 * @ORM\Table(name="plg_customer_coupon")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\CustomerCoupon42\Repository\CustomerCouponRepository")
 * @UniqueEntity("coupon_cd")
 */
class CustomerCoupon extends AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="coupon_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="coupon_cd", type="string", nullable=true, length=20, unique=true)
     */
    private $coupon_cd;

    /**
     * @var string
     *
     * @ORM\Column(name="coupon_name", type="string", nullable=true, length=50)
     */
    private $coupon_name;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_rate", type="decimal", nullable=true, precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $discount_rate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", options={"default":true})
     */
    private $visible;

    /**
     * @var float
     *
     * @ORM\Column(name="coupon_lower_limit", type="decimal", nullable=true, precision=12, scale=2, options={"unsigned":true,"default":0})
     */
    private $coupon_lower_limit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set coupon_cd.
     *
     * @param string $couponCd
     *
     * @return CustomerCoupon
     */
    public function setCouponCd($couponCd)
    {
        $this->coupon_cd = $couponCd;

        return $this;
    }

    /**
     * Get coupon_cd.
     *
     * @return string
     */
    public function getCouponCd()
    {
        return $this->coupon_cd;
    }

    /**
     * Set coupon_name.
     *
     * @param string $couponName
     *
     * @return CustomerCoupon
     */
    public function setCouponName($couponName)
    {
        $this->coupon_name = $couponName;

        return $this;
    }

    /**
     * Get coupon_name.
     *
     * @return string
     */
    public function getCouponName()
    {
        return $this->coupon_name;
    }

    /**
     * Set discount_rate.
     *
     * @param string $discountRate
     *
     * @return CustomerCoupon
     */
    public function setDiscountRate($discountRate)
    {
        $this->discount_rate = $discountRate;

        return $this;
    }

    /**
     * Get discount_rate.
     *
     * @return string
     */
    public function getDiscountRate()
    {
        return $this->discount_rate;
    }

    /**
     * Set del_flg.
     *
     * @param bool $visible
     *
     * @return CustomerCoupon
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get del_flg.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Set create_date.
     *
     * @param \DateTime $createDate
     *
     * @return CustomerCoupon
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date.
     *
     * @param \DateTime $updateDate
     *
     * @return CustomerCoupon
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * @return int
     */
    public function getCouponLowerLimit()
    {
        return $this->coupon_lower_limit;
    }

    /**
     * @param int $couponLowerLimit
     *
     * @return CustomerCoupon
     */
    public function setCouponLowerLimit($couponLowerLimit)
    {
        $this->coupon_lower_limit = $couponLowerLimit;

        return $this;
    }
}
