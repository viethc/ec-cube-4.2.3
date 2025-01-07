<?php
// src/Entity/CouponOrder.php

namespace Plugin\CustomerCoupon42\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="plg_customer_coupon_order")
 */
class CouponOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $coupon_id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $coupon_cd;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $coupon_name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $available_from_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $available_to_date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $date_of_use;

    /**
     * @ORM\Column(type="integer")
     */
    private $customer_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @ORM\ManyToOne(targetEntity="Coupon")
     * @ORM\JoinColumn(name="coupon_rate_id", referencedColumnName="coupon_rate_id")
     */
    private $coupon;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $update_date;

    // Getter and Setter methods
    public function getCouponId()
    {
        return $this->coupon_id;
    }

    public function setCouponId($coupon_id)
    {
        $this->coupon_id = $coupon_id;
    }

    public function getCouponCd()
    {
        return $this->coupon_cd;
    }

    public function setCouponCd($coupon_cd)
    {
        $this->coupon_cd = $coupon_cd;
    }

    public function getCouponName()
    {
        return $this->coupon_name;
    }

    public function setCouponName($coupon_name)
    {
        $this->coupon_name = $coupon_name;
    }

    public function getAvailableFromDate()
    {
        return $this->available_from_date;
    }

    public function setAvailableFromDate($available_from_date)
    {
        $this->available_from_date = $available_from_date;
    }

    public function getAvailableToDate()
    {
        return $this->available_to_date;
    }

    public function setAvailableToDate($available_to_date)
    {
        $this->available_to_date = $available_to_date;
    }

    public function getDateOfUse()
    {
        return $this->date_of_use;
    }

    public function setDateOfUse($date_of_use)
    {
        $this->date_of_use = $date_of_use;
    }

    public function getCustomerId()
    {
        return $this->customer_id;
    }

    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    public function getVisible()
    {
        return $this->visible;
    }

    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    public function getCoupon()
    {
        return $this->coupon;
    }

    public function setCouponRate($coupon)
    {
        $this->coupon = $coupon;
    }

    public function getCreateDate()
    {
        return $this->create_date;
    }

    public function setCreateDate($create_date)
    {
        $this->create_date = $create_date;
    }

    public function getUpdateDate()
    {
        return $this->update_date;
    }

    public function setUpdateDate($update_date)
    {
        $this->update_date = $update_date;
    }
}
