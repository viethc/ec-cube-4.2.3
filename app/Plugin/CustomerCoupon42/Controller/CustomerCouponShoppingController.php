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

namespace Plugin\CustomerCoupon42\Controller;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository;

/**
 * Class CustomerCouponShoppingController
 */
class CustomerCouponShoppingController extends AbstractController
{
    /**
     * @var CustomerCouponOrderRepository
     */
    private $customerCouponOrderReposity;

    /**
     * CustomerCouponOrderController constructor.
     *
     * @param CustomerCouponOrderRepository $customerCouponOrderReposity
     */
    public function __construct(CustomerCouponOrderRepository $customerCouponOrderReposity)
    {
        $this->customerCouponOrderReposity = $customerCouponOrderReposity;
    }

    /**
     * @param Request $request
     *
     * @Route("/mypage/mycoupon", name="plugin_customer_coupon_mycoupon")
     * @Template("@CustomerCoupon42/default/mypage_mycoupon.twig")
     */
    public function mycoupon(Request $request)
    {
        $couponsOrder = $this->customerCouponOrderReposity->findBy(
            ['visible' => true],
            ['id' => 'DESC']
        );

        return [
            'CouponsOrder' => $couponsOrder,
        ];
    }

    /**
     * Summary of shoppingCoupon
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|RedirectResponse
     * @Route("/plugin/customer-coupon/shopping-coupon", name="plugin_customer_coupon_shopping")
     * @Template("CustomerCoupon42/Resource/template/default/shopping_coupon.twig")
     */
    // public function shoppingCoupon(Request $request)
    // {

    // }
}
