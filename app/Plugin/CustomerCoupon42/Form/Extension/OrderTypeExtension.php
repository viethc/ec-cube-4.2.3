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

use Eccube\Request\Context;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Eccube\Form\Type\Shopping\OrderType;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository;

class OrderTypeExtension extends AbstractTypeExtension
{
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
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponRepository $customerCouponRepository
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository $customerCouponOrderReposity
     */
    public function __construct(
        CustomerCouponRepository $customerCouponRepository,
        CustomerCouponOrderRepository $customerCouponOrderReposity,
        Context $requestContext
    ) {
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

            $CustomerCoupons = $this->getCustomerCoupons();

            $form = $event->getForm();
            $this->addCustomerCouponForm($form, $CustomerCoupons->toArray());
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
            $form = $event->getForm();
            dd($form["CustomerCoupon"]);
        });
    }

    private function getCustomerCoupons()
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
                return $choice instanceof CustomerCoupon ? $choice->getCouponName() : $choice;
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
