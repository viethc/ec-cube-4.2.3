<?php

namespace Customize\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemHolderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Customize\Service\RegionalDiscountService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\DiscountProcessor;

class RegionalDiscountProcessor implements DiscountProcessor
{
    /**
     * @var RegionalDiscountService
     */
    protected $regionalDiscountService;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * DeliveryFeePreprocessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, RegionalDiscountService $regionalDiscountService)
    {
        $this->entityManager = $entityManager;
        $this->regionalDiscountService = $regionalDiscountService;
    }

    /**
     * {@inheritdoc}
     */
    public function removeDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        $this->regionalDiscountService->removeDiscountItem($itemHolder);
    }

    /**
     * {@inheritdoc}
     */
    public function addDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        $this->regionalDiscountService->addDiscountItem($itemHolder);
    }
}
