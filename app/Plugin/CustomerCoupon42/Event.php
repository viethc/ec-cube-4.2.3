<?php

namespace Plugin\CustomerCoupon42;

use Eccube\Entity\Order;
use Eccube\Entity\Customer;
use Eccube\Event\TemplateEvent;
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;
use Plugin\CustomerCoupon42\Service\CustomerCouponService;
use Symfony\Component\Workflow\Event\Event as CompletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository;

class Event implements EventSubscriberInterface
{
    /**
     * @var CustomerCouponRepository
     */
    private $customerCouponRepository;

    /**
     * @var CustomerCouponOrderRepository
     */
    private $customerCouponOrderReposity;

    /**
     * @var CustomerCouponService
     */
    private $customerCouponService;

    /**
     * Event constructor.
     *
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponRepository $customerCouponRepository
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository $customerCouponOrderRepository
     * @param \Plugin\CustomerCoupon42\Service\CustomerCouponService $customerCouponService
     */
    public function __construct(
        CustomerCouponRepository $customerCouponRepository,
        CustomerCouponOrderRepository $customerCouponOrderRepository,
        CustomerCouponService $customerCouponService
    ) {
        $this->customerCouponRepository = $customerCouponRepository;
        $this->customerCouponOrderReposity = $customerCouponOrderRepository;
        $this->customerCouponService = $customerCouponService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Mypage/index.twig' => 'onRenderMypageNav',
            'Mypage/history.twig' => 'onRenderMypageNav',
            'Mypage/favorite.twig' => 'onRenderMypageNav',
            'Mypage/change.twig' => 'onRenderMypageNav',
            'Mypage/delivery.twig' => 'onRenderMypageNav',
            'Mypage/withdraw.twig' => 'onRenderMypageNav',
            '@CustomerCoupon42/default/mypage_mycoupon.twig' => 'onRenderMypageNav',

            'Cart/index.twig' => 'onRenderCartNotice',
            'Shopping/index.twig' => 'onRenderShopping',
            'Shopping/confirm.twig' => 'onRenderShopping',

            'workflow.order.completed' => 'onOrderStateCompleted',
        ];
    }

    /**
     * Hook point add coupon information to mypage.
     *
     * @param TemplateEvent $event
     */
    public function onRenderMypageNav(TemplateEvent $event)
    {
        $event->addSnippet('@CustomerCoupon42/default/mypage_mycoupon_nav.twig');
    }

    /**
     * Hiển thị thông báo khi có coupon
     * Dựa trên giá trị đơn hàng, thông báo giá trị coupon sẽ nhận được khi hoàn tất đơn hàng
     *
     * @param TemplateEvent $event
     */
    public function onRenderCartNotice(TemplateEvent $event)
    {
        $parameters = $event->getParameters();

        $totalPrice = $parameters['totalPrice'];

        $CurrentCoupon = null;
        $NextCoupon = null;
        $CustomerCoupons = $this->customerCouponRepository->findByActiveCoupons();

        foreach ($CustomerCoupons as $index => $CustomerCoupon) {
            if ($totalPrice <= $CustomerCoupon->getCouponLowerLimit()) {
                if ($totalPrice === $CustomerCoupon->getCouponLowerLimit()) {
                    $CurrentCoupon = clone $CustomerCoupon;
                    if ($index < count($CustomerCoupons) - 1) {
                        $NextCoupon = clone $CustomerCoupons[$index + 1];
                    }
                } else {
                    $CurrentCoupon = clone $CustomerCoupons[$index > 0 ? $index - 1 : $index];
                    $NextCoupon = clone $CustomerCoupon;
                }

                break;
            }
        }

        $parameters['CurrentCoupon'] = $CurrentCoupon;
        $parameters['NextCoupon'] = $NextCoupon;

        // set parameter for twig files
        $event->setParameters($parameters);

        $event->addSnippet('@CustomerCoupon42/default/cart_notice.twig');
    }

    /**
     * Thêm snippet thông tin Coupon vào màn hình Shipping
     * 
     * @param \Eccube\Event\TemplateEvent $event
     * @return void
     */
    public function onRenderShopping(TemplateEvent $event)
    {
        $parameters = $event->getParameters();

        // 登録がない、レンダリングをしない
        /** @var Order $Order */
        $Order = $parameters['Order'];
        $CouponOrder = $this->customerCouponOrderReposity->getCouponOrder($Order->getPreOrderId());
        $parameters['CouponOrder'] = $CouponOrder;

        // set parameter for twig files
        $event->setParameters($parameters);

        if (strpos($event->getView(), 'index.twig') !== false) {
            $event->addSnippet('@CustomerCoupon42/default/customer_coupon_shopping_index.twig');
        } else {
            $event->addSnippet('@CustomerCoupon42/default/customer_coupon_shopping_confirm.twig');
        }
    }

    /**
     * Khi đơn hàng hoàn thành, kiểm tra giá trị đơn hàng và phát hành Coupon
     * 
     * @param \Symfony\Component\Workflow\Event\Event $event
     * @return void
     */
    public function onOrderStateCompleted(CompletedEvent $event): void
    {
        /** @var OrderStateMachineContext $context */
        $context = $event->getSubject();
        /** @var Order $Order */
        $Order = $context->getOrder();
        /** @var Customer $Customer */
        $Customer = $Order->getCustomer();

        $totalPrice = $Order->getSubtotal();

        /** @var CustomerCoupon $CurrentCoupon */
        $CurrentCoupon = $this->customerCouponRepository->findOneUseCoupon($totalPrice);

        if ($CurrentCoupon) {
            $this->customerCouponService->registCustomerCouponOrder($Order, $Customer, $CurrentCoupon);
        }
    }
}
