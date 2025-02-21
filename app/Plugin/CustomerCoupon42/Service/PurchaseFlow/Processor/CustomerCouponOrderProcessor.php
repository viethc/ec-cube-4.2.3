<?php

namespace Plugin\CustomerCoupon42\Service\PurchaseFlow\Processor;

use Eccube\Entity\Order;
use Eccube\Annotation\OrderFlow;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;

/**
 * Summary of CustomerCouponOrderProcessor
 * 
 * @OrderFlow
 */
class CustomerCouponOrderProcessor implements ItemHolderPreprocessor
{
    /*
     * ItemHolderPreprocessor
     */

    /**
     * Summary of process
     * @param \Eccube\Entity\ItemHolderInterface $itemHolder
     * @param \Eccube\Service\PurchaseFlow\PurchaseContext $context
     * @return void
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        switch ($itemHolder->getOrderStatus()->getId()) {
            case OrderStatus::NEW:
            case OrderStatus::PAID:
            case OrderStatus::IN_PROGRESS:
            case OrderStatus::DELIVERED:
            case OrderStatus::CANCEL:
            case OrderStatus::RETURNED:
                break;
            default:
                return;
        }

        dd("CustomerCouponOrderProcessor: ". $itemHolder->getOrderStatus()->getId() ."");
    }
}