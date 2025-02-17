<?php

namespace Plugin\CustomerCoupon42\Service\PurchaseFlow\Processor;

use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\Master\TaxType;
use Eccube\Annotation\ShoppingFlow;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Plugin\CustomerCoupon42\Entity\CustomerCouponOrder;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository;

/**
 * Summary of CustomerCouponProcessor
 * 
 * @ShoppingFlow
 */
class CustomerCouponProcessor extends ItemHolderValidator implements ItemHolderPreprocessor, PurchaseProcessor
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var CustomerCouponRepository
     */
    protected $customerCouponRepository;

    /**
     * @var CustomerCouponOrderRepository
     */
    protected $customerCouponOrderReposity;

    public function __construct(
        EntityManagerInterface $entityManager,
        CustomerCouponRepository $customerCouponRepository,
        CustomerCouponOrderRepository $customerCouponOrderReposity
    ) {
        $this->entityManager = $entityManager;
        $this->customerCouponRepository = $customerCouponRepository;
        $this->customerCouponOrderReposity = $customerCouponOrderReposity;
    }

    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        $CustomerCouponOrder = $this->customerCouponOrderReposity->getCouponOrder($itemHolder->getPreOrderId());

        if ($CustomerCouponOrder) {
            $this->removeCustomerCouponDiscountItem($itemHolder);
            $this->addCustomerCouponDiscountItem($itemHolder, $CustomerCouponOrder);
        }
    }

    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
    }

    public function prepare(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }
    }

    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        //
    }

    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        //
    }

    private function removeCustomerCouponDiscountItem(Order $itemHolder)
    {
        foreach ($itemHolder->getItems() as $item) {
            if (CustomerCouponProcessor::class === $item->getProcessorName()) {
                $itemHolder->removeOrderItem($item);
                $this->entityManager->remove($item);
            }
        }
    }

    private function addCustomerCouponDiscountItem(Order $itemHolder, CustomerCouponOrder $customerCouponOrder)
    {
        $CustomerCoupon = $this->customerCouponRepository->find($customerCouponOrder->getCouponId());

        $taxDisplayType = TaxDisplayType::INCLUDED; // 税込
        $taxType = TaxType::NON_TAXABLE; // 不課税
        $tax = 0;
        $taxRate = 0;
        $taxRuleId = null;
        $roundingType = null;
        $DiscountType = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, $taxDisplayType);
        $Taxation = $this->entityManager->find(TaxType::class, $taxType);

        $OrderItem = new OrderItem();
        $OrderItem->setProductName($CustomerCoupon->getCouponName())
            ->setPrice($customerCouponOrder->getDiscount() * -1)
            ->setQuantity(1)
            ->setTax($tax)
            ->setTaxRate($taxRate)
            ->setRoundingType($roundingType)
            ->setOrderItemType($DiscountType)
            ->setTaxDisplayType($TaxInclude)
            ->setTaxType($Taxation)
            ->setOrder($itemHolder)
            ->setProcessorName(CustomerCouponProcessor::class);

        $itemHolder->addItem($OrderItem);
    }
}