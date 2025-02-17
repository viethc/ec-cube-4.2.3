<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomerCoupon42\Form\Extension;

use Eccube\Entity\Order;
use Eccube\Request\Context;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Form\Type\Shopping\OrderType;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;
use Symfony\Component\Validator\Constraints\NotBlank;
use Plugin\CustomerCoupon42\Entity\CustomerCouponOrder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository;

class OrderTypeExtension extends AbstractTypeExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CustomerCouponRepository
     */
    protected $customerCouponRepository;

    /**
     * @var CustomerCouponOrderRepository
     */
    protected $customerCouponOrderReposity;

    /**
     * @var Context
     */
    protected $requestContext;

    /**
     * Constructor
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponRepository $customerCouponRepository
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository $customerCouponOrderReposity
     * @param \Eccube\Request\Context $requestContext
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CustomerCouponRepository $customerCouponRepository,
        CustomerCouponOrderRepository $customerCouponOrderReposity,
        Context $requestContext
    ) {
        $this->entityManager = $entityManager;
        $this->customerCouponRepository = $customerCouponRepository;
        $this->customerCouponOrderReposity = $customerCouponOrderReposity;
        $this->requestContext = $requestContext;
    }

    public function getExtendedType()
    {
        return OrderType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [OrderType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // 支払い方法のプルダウンを生成
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
          /** @var Order $Order */
            $Order = $event->getData();
            if (null === $Order || !$Order->getId()) {
                return;
            }

            // Trường hợp Order đã được áp dụng Coupon nào đó
            $CustomerCouponOptionCurrent = null;
            $CustomerCouponOrder = $this->customerCouponOrderReposity->getCouponOrder($Order->getPreOrderId());
            if (null !== $CustomerCouponOrder) {
                $CustomerCouponOptionCurrent = $this->customerCouponRepository->find($CustomerCouponOrder->getCouponId());
            }

            $CustomerCouponOptions = $this->getCustomerCouponOptions();

            $form = $event->getForm();
            $this->addCustomerCouponForm($form, $CustomerCouponOptions->toArray(), $CustomerCouponOptionCurrent);
        });

        // 配送方法の選択によって使用できる支払い方法がかわるため, フォームを再生成する.
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
          /** @var Order $Order */
            $Order = $event->getForm()->getData();
            $data = $event->getData();

            $form = $event->getForm();
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Order $Order */
            $Order = $event->getForm()->getData();

            $CustomerCouponOrder = $this->customerCouponOrderReposity->getCouponOrder($Order->getPreOrderId());

            $form = $event->getForm();
            if ($form->has("CustomerCoupon")) {
                $CustomerCouponSelected = $form->get("CustomerCoupon")->getData();
                $this->updateCustomerCouponOrder($Order, $CustomerCouponOrder, $CustomerCouponSelected);
            }
        });
    }

    private function updateCustomerCouponOrder(Order $Order, ?CustomerCouponOrder $CustomerCouponOrder, CustomerCoupon|string $CustomerCoupon)
    {
        if ($CustomerCouponOrder) {
            if (!($CustomerCoupon instanceof CustomerCoupon) || $CustomerCouponOrder->getCouponId() !== $CustomerCoupon->getId()) {
                $CustomerCouponOrder->setUseOrderId(0);
                $CustomerCouponOrder->setPreUseOrderId(null);
                $CustomerCouponOrder->setDiscount(0);
                // $CustomerCouponOrder->setDateOfUse(null);
            }
        } else {
            $CustomerCouponOrder = $this->customerCouponOrderReposity->findByCoupon($CustomerCoupon->getId());
            $CustomerCouponOrder->setUseOrderId($Order->getId());
            $CustomerCouponOrder->setPreUseOrderId($Order->getPreOrderId());
            $CustomerCouponOrder->setDiscount(0);
            // $CustomerCouponOrder->setDateOfUse(new \DateTime());
        }

        $this->entityManager->persist($CustomerCouponOrder);
        $this->entityManager->flush();
    }

    private function getCustomerCouponOptions()
    {
        $CustomerCoupons = [];
        $CustomerCouponOrders = [];

        $Customer = $this->requestContext->getCurrentUser();
        if ($Customer !== null) {
            $CustomerCouponOrders = $this->customerCouponOrderReposity->findByCustomer($Customer->getId());
        }

        foreach ($CustomerCouponOrders as $CouponOrder) {
            $CustomerCoupons[$CouponOrder->getId()][] = $this->customerCouponRepository->find($CouponOrder->getCouponId());
        }

        if (empty($CustomerCoupons)) {
            return new ArrayCollection();
        }

        $i = 0;
        $CouponsIntersected = [];
        foreach ($CustomerCoupons as $CustomerCoupon) {
            if ($i === 0) {
                $CouponsIntersected = $CustomerCoupon;
            } else {
                $CouponsIntersected = array_intersect($CouponsIntersected, $CustomerCoupon);
            }
            $i++;
        }

        return new ArrayCollection($CouponsIntersected);
    }

    private function addCustomerCouponForm(FormInterface $form, array $choices, CustomerCoupon $customerCoupon = null)
    {
        $message = trans('plugin_customer_coupon.front.shopping_customer_coupon.unselected');

        if (empty($choices)) {
            $message = trans('plugin_customer_coupon.front.shopping_customer_coupon.notfound');
        }

        $form->add('CustomerCoupon', ChoiceType::class, [
            // 'class' => CustomerCoupon::class,
            'choice_value' => function ($choice) {
                return $choice instanceof CustomerCoupon ? $choice->getId() : 0;
            },
            'choice_label' => function ($choice) {
                return $choice instanceof CustomerCoupon ? $choice->getCouponName() . "（". $choice->getCouponCd() . "）" : $choice;
            },
            'expanded' => true,
            'multiple' => false,
            'placeholder' => false,
            'mapped' => false,
            'constraints' => [
                new NotBlank(['message' => $message]),
            ],
            'choices' => array_merge(
                ['0' => trans('plugin_customer_coupon.front.shopping_customer_coupon.notuse')],
                $choices
            ),
            'data' => $customerCoupon,
            'invalid_message' => $message,
        ]);
    }
}
