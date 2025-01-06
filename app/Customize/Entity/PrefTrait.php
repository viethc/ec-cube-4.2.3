<?php

namespace Customize\Entity;

use Eccube\Entity\Master\Pref;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Customize pref entity
 * @author trungnq <trungnq@unitech.vn>
 * 
 * @EntityExtension("Eccube\Entity\Master\Pref")
 */
trait PrefTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Customize\Entity\RegionalDiscount", mappedBy="Pref", cascade={"remove"})
     * @ORM\OrderBy({
     *     "amount"="DESC"
     * })
     */
    private $RegionalDiscounts;

    /**
     * __construct
     *
     * 
     */
    public function __construct()
    {
        $this->RegionalDiscounts = new ArrayCollection();
    }

    /**
     * Get regional discounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegionalDiscounts(): Collection
    {
        return $this->RegionalDiscounts;
    }

    /**
     * Add regional discount
     *
     * @param \Customize\Entity\RegionalDiscount $regionalDiscount
     * 
     * @return Supplier
     * 
     */
    public function addRegionalDiscount(RegionalDiscount $regionalDiscount): Pref
    {
        $this->RegionalDiscounts[] = $regionalDiscount;

        return $this;
    }

    /**
     * Remove regional discount
     *
     * @param \Customize\Entity\RegionalDiscount $regionalDiscount
     * 
     * @return bool
     * 
     */
    public function removeRegionalDiscount(RegionalDiscount $regionalDiscount): bool
    {
        return $this->RegionalDiscounts->removeElement($regionalDiscount);
    }
}
