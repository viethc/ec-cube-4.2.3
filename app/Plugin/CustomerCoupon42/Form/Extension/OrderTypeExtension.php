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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Form\Type\Shopping\OrderType;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;
use Symfony\Component\Validator\Constraints\NotBlank;
use Plugin\CustomerCoupon42\Entity\CustomerCouponOrder;
use Plugin\CustomerCoupon42\Service\CustomerCouponService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository;

class OrderTypeExtension extends AbstractTypeExtension
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CustomerCouponRepository
     */
    private $customerCouponRepository;

    /**
     * @var CustomerCouponOrderRepository
     */
    private $customerCouponOrderRepository;

    /**
     * @var CustomerCouponService
     */
    private $customerCouponService;

    /**
     * @var Context
     */
    private $requestContext;

    /**
     * Constructor
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponRepository $customerCouponRepository
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository $customerCouponOrderRepository
     * @param \Plugin\CustomerCoupon42\Service\CustomerCouponService $customerCouponService
     * @param \Eccube\Request\Context $requestContext
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CustomerCouponRepository $customerCouponRepository,
        CustomerCouponOrderRepository $customerCouponOrderRepository,
        CustomerCouponService $customerCouponService,
        Context $requestContext
    ) {
        $this->entityManager = $entityManager;
        $this->customerCouponRepository = $customerCouponRepository;
        $this->customerCouponOrderRepository = $customerCouponOrderRepository;
        $this->customerCouponService = $customerCouponService;
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

    /**
     * Thêm control vào form dựa trên sự kiện
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Nếu được gọi từ ShoppingController::checkout thì bỏ qua
        if ($options['skip_add_form']) {
            return;
        }

        // POST_SET_DATA
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
          /** @var Order $Order */
            $Order = $event->getData();
            if (null === $Order || !$Order->getId()) {
                return;
            }

            // Trường hợp Order đã được áp dụng Coupon nào đó
            $CustomerCouponOptionCurrent = null;
            $CustomerCouponOrder = $this->customerCouponOrderRepository->getCouponOrder($Order->getPreOrderId());

            // Trường hợp tồn tại thì get `CustomerCoupon` cho selected option
            if ($CustomerCouponOrder) {
                $CustomerCouponOptionCurrent = $this->customerCouponRepository->find($CustomerCouponOrder->getCouponId());
            } else {
                // Check trường hợp `CustomerCouponOrder` đã được chọn nhưng mất session
                $CustomerCouponOrder = $this->customerCouponOrderRepository->findByLostSession();

                // Nếu có thì clear thông tin Order
                if ($CustomerCouponOrder) {
                    $this->updateCustomerCouponOrder($Order, $CustomerCouponOrder);
                }
            }

            // Lấy danh sách các CustomerCoupon hiệu lực để tạo data options
            $CustomerCouponOptions = $this->getCustomerCouponOptions();

            $form = $event->getForm();
            $this->addCustomerCouponForm($form, $CustomerCouponOptions->toArray(), $CustomerCouponOptionCurrent);
        });

        // POST_SUBMIT
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Order $Order */
            $Order = $event->getForm()->getData();

            $CustomerCouponOrder = $this->customerCouponOrderRepository->getCouponOrder($Order->getPreOrderId());

            $form = $event->getForm();
            if ($form->has("CustomerCoupon")) {
                $CustomerCouponSelected = $form->get("CustomerCoupon")->getData();
                $this->updateCustomerCouponOrder($Order, $CustomerCouponOrder, $CustomerCouponSelected);
            }
        });
    }

    /**
     * Cập nhật lại thông tin của Customer Coupon Order
     * 
     * @param \Eccube\Entity\Order $Order
     * @param \Plugin\CustomerCoupon42\Entity\CustomerCouponOrder|null $CustomerCouponOrder
     * @param \Plugin\CustomerCoupon42\Entity\CustomerCoupon|string|null $CustomerCoupon
     * @return void
     */
    private function updateCustomerCouponOrder(Order $Order, ?CustomerCouponOrder $CustomerCouponOrder, $CustomerCoupon = null)
    {
        $clearFlg = false;
        $renewFlg = false;
        $resetFlg = false;

        if ($CustomerCouponOrder) {
            if ($CustomerCoupon instanceof CustomerCoupon) {
                if ($CustomerCouponOrder->getCouponId() !== $CustomerCoupon->getId()) {
                    $clearFlg = true;
                    $renewFlg = true;
                } else {
                    $resetFlg = true;
                }
            } else {
                $clearFlg = true;
            }
        } else {
            if ($CustomerCoupon instanceof CustomerCoupon) {
                $renewFlg = true;
            }
        }

        // Trường hợp chọn "Không sử dụng Coupon" hoặc chọn sử dụng Coupon khác với Coupon đã lưu
        // thì clear thông tin Order khỏi CustomerCouponOrder hiện tại
        if ($clearFlg) {
            $CustomerCouponOrder->setUseOrderId(0);
            $CustomerCouponOrder->setPreUseOrderId(null);
            $CustomerCouponOrder->setDiscount(0);
            $this->customerCouponOrderRepository->save($CustomerCouponOrder);
        }

        if ($renewFlg || $resetFlg) {
            // Trường hợp chọn sử dụng Coupon mà khác với Coupon đã lưu
            // thì get lại CustomerCouponOrder tương ứng
            if ($renewFlg) {
                $Customer = $this->requestContext->getCurrentUser();
                $CustomerCouponOrder = $this->customerCouponOrderRepository->findByCustomerCoupon($Customer, $CustomerCoupon->getId());
            }
            $CustomerCouponOrder->setUseOrderId($Order->getId());
            $CustomerCouponOrder->setPreUseOrderId($Order->getPreOrderId());
            $CustomerCouponOrder->setDiscount($this->customerCouponService->calcDiscount($CustomerCoupon, $Order->getSubtotal()));
            $this->customerCouponOrderRepository->save($CustomerCouponOrder);
        }
    }

    /**
     * Get danh sách Customer Coupon theo khách hàng để tạo các Option cho Radio button trên $form
     * 
     * @return ArrayCollection<TKey, mixed>|ArrayCollection<TKey, T>
     */
    private function getCustomerCouponOptions()
    {
        $CustomerCoupons = [];
        $CustomerCouponOrders = [];

        $Customer = $this->requestContext->getCurrentUser();
        if ($Customer !== null) {
            $CustomerCouponOrders = $this->customerCouponOrderRepository->getOptionsByCustomer($Customer);
        }

        foreach ($CustomerCouponOrders as $CouponOrder) {
            $CustomerCoupons[$CouponOrder->getId()] = $this->customerCouponRepository->find($CouponOrder->getCouponId());
        }

        if (empty($CustomerCoupons)) {
            return new ArrayCollection();
        }

        $CouponsUnique = array_reduce($CustomerCoupons, function ($carry, $CustomerCoupon) {
            $carry[$CustomerCoupon->getId()] = $CustomerCoupon;
            return $carry;
        }, []);

        return new ArrayCollection($CouponsUnique);
    }

    /**
     * Thêm item CustomerCoupon vào $form
     * 
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $choices
     * @param \Plugin\CustomerCoupon42\Entity\CustomerCoupon|null $customerCoupon
     * @return void
     */
    private function addCustomerCouponForm(FormInterface $form, array $choices, CustomerCoupon $customerCoupon = null)
    {
        $message = trans('plugin_customer_coupon.front.shopping_customer_coupon.unselected');

        if (empty($choices)) {
            $message = trans('plugin_customer_coupon.front.shopping_customer_coupon.notfound');
        }

        $form->add('CustomerCoupon', ChoiceType::class, [
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
            'choices' => array_merge(
                ['0' => trans('plugin_customer_coupon.front.shopping_customer_coupon.notuse')],
                $choices
            ),
            'data' => $customerCoupon,
            'invalid_message' => $message,
        ]);
    }
}
