<?php

namespace Customize\Service;

use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\TaxDisplayType;
use Customize\Repository\RegionalDiscountRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Customize\Service\PurchaseFlow\Processor\RegionalDiscountProcessor;

class RegionalDiscountService
{
    /**
     * @var RegionalDiscountRepository
     */
    protected $regionalDiscountRepository;

    /**
     * @var TranslatorInterface
     */
    protected $translatorInterface;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Discount service constructor.
     *
     * @param RegionalDiscountRepository $regionalDiscountRepository
     * @param TranslatorInterface $translatorInterface
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        RegionalDiscountRepository $regionalDiscountRepository,
        TranslatorInterface $translatorInterface,
        EntityManagerInterface $entityManager
    ){
        $this->regionalDiscountRepository = $regionalDiscountRepository;
        $this->translatorInterface = $translatorInterface;
        $this->entityManager = $entityManager;
    }

    /**
     * 既存のポイント明細を削除する.
     *
     * @param ItemHolderInterface $itemHolder
     */
    public function removeDiscountItem(ItemHolderInterface $itemHolder): void
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        foreach ($itemHolder->getItems() as $item) {
            if (RegionalDiscountProcessor::class === $item->getProcessorName()) {
                $itemHolder->removeOrderItem($item);
                $this->entityManager->remove($item);
            }
        }
    }

    /**
     * 明細追加処理.
     *
     * @param ItemHolderInterface $itemHolder
     */
    public function addDiscountItem(ItemHolderInterface $itemHolder): void
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        $DeliveryFeeType = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);

        $amount = 0;
        foreach ($itemHolder->getShippings() as $Shipping) {
            $highestDiscount = $this->regionalDiscountRepository->findHighestDiscount($Shipping->getPref());
            $amount = $highestDiscount ? $highestDiscount->getAmount() : 0;
        }

        $OrderItem = new OrderItem();
        $OrderItem->setProductName($this->translatorInterface->trans('regional_discount'))
            ->setPrice($amount * -1)
            ->setQuantity(1)
            ->setOrderItemType($DeliveryFeeType)
            ->setOrder($itemHolder)
            ->setTaxDisplayType($TaxInclude)
            ->setTaxType($Taxation)
            ->setProcessorName(RegionalDiscountProcessor::class);

        // Add order item
        $itemHolder->addItem($OrderItem);
    }
}
