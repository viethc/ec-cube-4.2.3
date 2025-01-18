<?php

namespace Plugin\CustomerCoupon42;

use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'order' => [
                'children' => [
                    'plugin_customer_coupon' => [
                        'name' => 'クーポン管理',
                        'url' => 'plugin_customer_coupon_list',
                    ],
                    'plugin_customer_coupon_order' => [
                        'name' => '顧客のクーポン一覧',
                        'url' => 'plugin_customer_coupon_order_list',
                    ],
                ],
            ],
        ];
    }
}
