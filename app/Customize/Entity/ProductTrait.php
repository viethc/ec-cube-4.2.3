<?php

namespace Customize\Entity;

use Eccube\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
 * Customize product entity
 * @author trungnq <trungnq@unitech.vn>
 * 
 * @EntityExtension("Eccube\Entity\Product")
 */
trait ProductTrait
{
    /**
     * @var \Customize\Entity\Supplier
     *
     * @ORM\ManyToOne(targetEntity="Customize\Entity\Supplier", inversedBy="Products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="supplier_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $Supplier;

    /**
     * Get supplier.
     *
     * @return \Customize\Entity\Supplier|null
     */
    public function getSupplier(): ?Supplier
    {
        return $this->Supplier;
    }

    /**
     * Set supplier.
     *
     * @param \Customize\Entity\Supplier|null $supplier
     *
     * @return Product
     */
    public function setSupplier(Supplier $supplier = null): Product
    {
        $this->Supplier = $supplier;

        return $this;
    }
}
