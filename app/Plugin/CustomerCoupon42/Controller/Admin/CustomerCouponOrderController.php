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

namespace Plugin\CustomerCoupon42\Controller\Admin;

use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class CustomerCouponOrderController
 */
class CustomerCouponOrderController extends AbstractController
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
     * @return array
     * @Route("/%eccube_admin_route%/plugin/customer-coupon-order", name="plugin_customer_coupon_order_list")
     * @Template("@CustomerCoupon42/admin/coupon-order-index.twig")
     */
    public function index(Request $request)
    {
        $couponsOrder = $this->customerCouponOrderReposity->findBy(
            ['visible' => true],
            ['id' => 'DESC']
        );

        return [
            'CouponsOrder' => $couponsOrder,
        ];
    }
}
