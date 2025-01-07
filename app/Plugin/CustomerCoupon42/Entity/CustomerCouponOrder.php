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

use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Customer Coupon Order
 *
 * @ORM\Table(name="plg_customer_coupon_order")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository")
 */
class CustomerCouponOrder extends AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="coupon_order_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="coupon_id", type="integer", options={"unsigned":true})
     */
    private $coupon_id;

    /**
     * @var string
     *
     * @ORM\Column(name="coupon_cd", type="string", nullable=true, length=20)
     */
    private $coupon_cd;

    /**
     * @var string
     *
     * @ORM\Column(name="coupon_name", type="string", nullable=true, length=50)
     */
    private $coupon_name;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer", options={"unsigned":true}, nullable=true)
     */
    private $customer_id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer", options={"unsigned":true})
     */
    private $order_id;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
     */
    private $discount = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="available_from_date", type="datetimetz")
     */
    private $available_from_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="available_to_date", type="datetimetz")
     */
    private $available_to_date;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_of_use", type="datetimetz", nullable=true)
     */
    private $date_of_use;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", options={"default":true})
     */
    private $visible;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set coupon_id.
     *
     * @param int $couponId
     *
     * @return CustomerCouponOrder
     */
    public function setCouponId($couponId)
    {
        $this->coupon_id = $couponId;

        return $this;
    }

    /**
     * Get coupon_id.
     *
     * @return int
     */
    public function getCouponId()
    {
        return $this->coupon_id;
    }

    /**
     * Set coupon_cd.
     *
     * @param string $couponCd
     *
     * @return CustomerCouponOrder
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
     * Set user_id.
     *
     * @param int $userId
     *
     * @return CustomerCouponOrder
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get customer_id.
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return CustomerCouponOrder
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set order_id.
     *
     * @param int $orderId
     *
     * @return CustomerCouponOrder
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get order_id.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set discount.
     *
     * @param string $discount
     *
     * @return CustomerCouponOrder
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount.
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set available_from_date.
     *
     * @param \DateTime $availableFromDate
     *
     * @return CustomerCouponOrder
     */
    public function setAvailableFromDate($availableFromDate)
    {
        $this->available_from_date = $availableFromDate;

        return $this;
    }

    /**
     * Get available_from_date.
     *
     * @return \DateTime
     */
    public function getAvailableFromDate()
    {
        return $this->available_from_date;
    }

    /**
     * Set available_to_date.
     *
     * @param \DateTime $availableToDate
     *
     * @return CustomerCouponOrder
     */
    public function setAvailableToDate($availableToDate)
    {
        $this->available_to_date = $availableToDate;

        return $this;
    }

    /**
     * Get available_to_date.
     *
     * @return \DateTime
     */
    public function getAvailableToDate()
    {
        return $this->available_to_date;
    }

    /**
     * Set date_of_use.
     *
     * @param \DateTime|null $dateOfUse
     *
     * @return CustomerCouponOrder
     */
    public function setDateOfUse($dateOfUse)
    {
        $this->date_of_use = $dateOfUse;

        return $this;
    }

    /**
     * Get date_of_use.
     *
     * @return \DateTime|null
     */
    public function getDateOfUse()
    {
        return $this->date_of_use;
    }

    /**
     * Set del_flg.
     *
     * @param bool $visible
     *
     * @return CustomerCouponOrder
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * is visible.
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
     * @return CustomerCouponOrder
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
     * @return CustomerCouponOrder
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
     * @return string
     */
    public function getCouponName()
    {
        return $this->coupon_name;
    }

    /**
     * @param string $coupon_name
     *
     * @return $this
     */
    public function setCouponName($coupon_name)
    {
        $this->coupon_name = $coupon_name;

        return $this;
    }
}
