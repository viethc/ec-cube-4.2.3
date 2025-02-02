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

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Eccube\Form\Type\Shopping\OrderType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrderTypeExtension extends AbstractTypeExtension
{
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

            $form = $event->getForm();
        });

        // 配送方法の選択によって使用できる支払い方法がかわるため, フォームを再生成する.
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
          /** @var Order $Order */
            $Order = $event->getForm()->getData();
            $data = $event->getData();

            $form = $event->getForm();
        });

        $builder->remove('Coupon');
        $builder->add(
            'Coupon',
            TextType::class,
            [
                'required' => false,
                'mapped' => false,
            ]
        );
    }
}
