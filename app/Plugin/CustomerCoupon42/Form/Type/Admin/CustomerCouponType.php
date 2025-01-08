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

namespace Plugin\CustomerCoupon42\Form\Type\Admin;

use Eccube\Form\Type\PriceType;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CouponType.
 */
class CustomerCouponType extends AbstractType
{
    /**
     * @var CustomerCouponRepository
     */
    private $couponRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ContainerBagInterface
     */
    private $container;

    /**
     * CustomerCouponType constructor.
     *
     * @param CustomerCouponRepository $couponRepository
     * @param ValidatorInterface $validator
     * @param ContainerBagInterface $container
     */
    public function __construct(CustomerCouponRepository $couponRepository, ValidatorInterface $validator, ContainerBagInterface $container)
    {
        $this->couponRepository = $couponRepository;
        $this->validator = $validator;
        $this->container = $container;
    }

    /**
     * buildForm.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currency = $this->container->get('currency');
        $builder
            ->add('coupon_cd', TextType::class, [
                'label' => 'plugin_customer_coupon.admin.label.coupon_cd',
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^[a-zA-Z0-9]+$/i']),
                ],
            ])
            ->add('coupon_name', TextType::class, [
                'label' => 'plugin_customer_coupon.admin.label.coupon_name',
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('coupon_lower_limit', PriceType::class, [
                'label' => 'plugin_customer_coupon.admin.label.coupon_lower_limit',
                'required' => true,
                'currency' => $currency,
                'constraints' => [
                    new Assert\Range([
                        'min' => 0,
                    ]),
                ],
            ])
            ->add('discount_rate', IntegerType::class, [
                'label' => 'plugin_customer_coupon.admin.label.discount_rate',
                'required' => true,
                'constraints' => [
                    new Assert\Range([
                        'min' => 1,
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('coupon_release', IntegerType::class, [
                'label' => 'plugin_customer_coupon.admin.label.coupon_release',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 1000000,
                    ]),
                ],
            ])
            ->add('coupon_use_time', HiddenType::class, [])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $form->getData();

                // 値引率
                /** @var ConstraintViolationList $errors */
                $errors = $this->validator->validate($data['discount_rate'], [
                    new Assert\NotBlank(),
                ]);
                if ($errors->count() > 0) {
                    foreach ($errors as $error) {
                        $form['discount_rate']->addError(new FormError($error->getMessage()));
                    }
                }
            });
    }

    /**
     * configureOptions
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Plugin\CustomerCoupon42\Entity\CustomerCoupon',
        ]);
    }

    /**
     * getName.
     *
     * @return string
     */
    public function getName()
    {
        return 'admin_plugin_customer_coupon';
    }
}
