<?php
// src/Entity/Coupon.php

namespace Plugin\CustomerCoupon42\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="plg_customer_coupon")
 */
class Coupon
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $coupon_rate_id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $coupon_rate_name;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $minimum_amount;

    /**
     * @ORM\Column(type="integer")
     */
    private $rate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $update_date;

    // Getter and Setter methods
    public function getCouponRateId()
    {
        return $this->coupon_rate_id;
    }

    public function setCouponRateId($coupon_rate_id)
    {
        $this->coupon_rate_id = $coupon_rate_id;
    }

    public function getCouponRateName()
    {
        return $this->coupon_rate_name;
    }

    public function setCouponRateName($coupon_rate_name)
    {
        $this->coupon_rate_name = $coupon_rate_name;
    }

    public function getMinimumAmount()
    {
        return $this->minimum_amount;
    }

    public function setMinimumAmount($minimum_amount)
    {
        $this->minimum_amount = $minimum_amount;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    public function getVisible()
    {
        return $this->visible;
    }

    public function setVisible($visible)
    {
        $this->visible = $visible;
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
