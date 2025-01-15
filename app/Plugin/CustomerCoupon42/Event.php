<?php

namespace Plugin\CustomerCoupon42;

use Eccube\Entity\Order;
use Eccube\Event\TemplateEvent;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Event implements EventSubscriberInterface
{
    /**
     * @var CustomerCouponRepository
     */
    private $customerCouponRepository;

    /**
     * Event constructor.
     *
     * @param CustomerCouponRepository $customerCouponRepository
     */
    public function __construct(CustomerCouponRepository $customerCouponRepository)
    {
        $this->customerCouponRepository = $customerCouponRepository;
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

        $CurrentCoupon = $this->customerCouponRepository->findOneActiveCoupon($totalPrice);
        $parameters['CurrentCoupon'] = $CurrentCoupon;

        $NextCoupon = $this->customerCouponRepository->findOneActiveCoupon($totalPrice, 'ASC');
        $parameters['NextCoupon'] = $NextCoupon;

        // set parameter for twig files
        $event->setParameters($parameters);

        $event->addSnippet('@CustomerCoupon42/default/cart_notice.twig');
    }

    public function onRenderShopping(TemplateEvent $event)
    {
        $parameters = $event->getParameters();
        
        if (strpos($event->getView(), 'index.twig') !== false) {
            $event->addSnippet('@CustomerCoupon42/default/customer_coupon_shopping_item.twig');
        } else {
            $event->addSnippet('@CustomerCoupon42/default/customer_coupon_shopping_item_confirm.twig');
        }
    }
}
