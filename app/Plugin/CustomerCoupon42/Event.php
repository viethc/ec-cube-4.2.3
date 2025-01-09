<?php

namespace Plugin\CustomerCoupon42;

use Eccube\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Event implements EventSubscriberInterface
{
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
}
