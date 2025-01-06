<?php

 namespace Customize\Entity;

 use Doctrine\ORM\Mapping as ORM;
 use Eccube\Entity\AbstractEntity;
 use Eccube\Entity\Master\Pref;
 use DateTime;

/**
 * Regional discount entity
 * 
 * @author trungnq <trungnq@unitech.vn>
 *
 * @ORM\Table(name="dtb_regional_discount")
 * @ORM\Entity(repositoryClass="Customize\Repository\RegionalDiscountRepository")
 */
class RegionalDiscount extends AbstractEntity
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
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=12, scale=2, options={"unsigned":true})
     */
    private $amount;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start_date", type="datetimetz")
     */
    private $start_date;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="end_date", type="datetimetz")
     */
    private $end_date;

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
     * @var \Eccube\Entity\Master\Pref
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref", inversedBy="RegionalDiscounts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pref_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $Pref;

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
     * Get amount
     *
     * @return float
     * 
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Set fee
     *
     * @param float $amount
     * 
     * @return Supplier
     * 
     */
    public function setAmount(float $amount): RegionalDiscount
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Set start date.
     *
     * @param DateTime $startDate
     *
     * @return Supplier
     */
    public function setStartDate($startDate): RegionalDiscount
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get start date.
     *
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->start_date;
    }

    /**
     * Set end date.
     *
     * @param DateTime $endDate
     *
     * @return Supplier
     */
    public function setEndDate($endDate): RegionalDiscount
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get end date.
     *
     * @return DateTime
     */
    public function getEndDate(): DateTime
    {
        return $this->end_date;
    }

    /**
     * Set create date.
     *
     * @param DateTime $createDate
     *
     * @return Supplier
     */
    public function setCreateDate($createDate): RegionalDiscount
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
    public function setUpdateDate($updateDate): RegionalDiscount
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
     * Set pref.
     *
     * @param \Eccube\Entity\Master\Pref|null $pref
     *
     * @return RegionalDiscount
     */
    public function setPref(Pref $pref = null): RegionalDiscount
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get pref.
     *
     * @return \Eccube\Entity\Master\Pref|null
     */
    public function getPref(): ?Pref
    {
        return $this->Pref;
    }
}
