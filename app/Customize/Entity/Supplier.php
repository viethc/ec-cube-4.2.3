<?php

namespace Customize\Entity;

use DateTime;
use Eccube\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Supplier entity
 * 
 * @author trungnq <trungnq@unitech.vn>
 *
 * @ORM\Table(name="dtb_supplier")
 * @ORM\Entity(repositoryClass="Customize\Repository\SupplierRepository")
 */
class Supplier extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=14, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\Product", mappedBy="Supplier", cascade={"remove"})
     * @ORM\OrderBy({
     *     "id"="ASC"
     * })
     */
    private $Products;

    /**
     * __construct
     *
     * 
     */
    public function __construct()
    {
        $this->Products = new ArrayCollection();
    }

    /**
     * Get ID
     *
     * @return int
     * 
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string|null
     * 
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * 
     * @return Supplier
     * 
     */
    public function setName(string $name): Supplier
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get email
     *
     * @return string|null
     * 
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string|null $email
     * 
     * @return Supplier
     * 
     */
    public function setEmail(?string $email): Supplier
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string|null
     * 
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string|null $phone
     * 
     * @return Supplier
     * 
     */
    public function setPhone(?string $phone): Supplier
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get address
     *
     * @return string|null
     * 
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string|null $address
     * 
     * @return Supplier
     * 
     */
    public function setAddress(?string $address): Supplier
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Set create date.
     *
     * @param DateTime $createDate
     *
     * @return Supplier
     */
    public function setCreateDate($createDate): Supplier
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create date.
     *
     * @return DateTime
     */
    public function getCreateDate(): DateTime
    {
        return $this->create_date;
    }

    /**
     * Set update date.
     *
     * @param DateTime $updateDate
     *
     * @return Supplier
     */
    public function setUpdateDate($updateDate): Supplier
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update date.
     *
     * @return DateTime
     */
    public function getUpdateDate(): DateTime
    {
        return $this->update_date;
    }

    /**
     * Get Products
     *
     * @return \Doctrine\Common\Collections\Collection
     * 
     */
    public function getProducts(): Collection
    {
        return $this->Products;
    }

    /**
     * Add product
     *
     * @param \Eccube\Entity\Product $product
     * 
     * @return Supplier
     * 
     */
    public function addProduct(Product $product): Supplier
    {
        $this->Products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \Eccube\Entity\Product $product
     * 
     * @return bool
     * 
     */
    public function removeProduct(Product $product): bool
    {
        return $this->Products->removeElement($product);
    }
}