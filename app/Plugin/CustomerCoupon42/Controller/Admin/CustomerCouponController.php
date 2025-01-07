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
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;
use Plugin\CustomerCoupon42\Form\Type\Admin\CustomerCouponType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Plugin\CustomerCoupon42\Service\CustomerCouponService;

/**
 * Class CustomerCouponController
 */
class CustomerCouponController extends AbstractController
{
    /**
     * @var CustomerCouponRepository
     */
    private $couponRepository;

    /**
     * @var CustomerCouponService
     */
    private $customerCouponService;

    /**
     * CustomerCouponController constructor.
     *
     * @param CustomerCouponRepository $couponRepository
     */
    public function __construct(CustomerCouponRepository $couponRepository, CustomerCouponService $customerCouponService)
    {
        $this->couponRepository = $couponRepository;
        $this->customerCouponService = $customerCouponService;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @Route("/%eccube_admin_route%/plugin/customer-coupon", name="plugin_customer_coupon_list")
     * @Template("@CustomerCoupon42/admin/index.twig")
     */
    public function index(Request $request)
    {
        $coupons = $this->couponRepository->findBy(
            ['visible' => true],
            ['id' => 'DESC']
        );

        return [
            'Coupons' => $coupons,
        ];
    }

    /**
     * クーポンの新規作成/編集確定
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     * @Route("/%eccube_admin_route%/plugin/customer-coupon/new", name="plugin_customer_coupon_new")
     * @Route("/%eccube_admin_route%/plugin/customer-coupon/{id}/edit", name="plugin_customer_coupon_edit", requirements={"id" = "\d+"})
     */
    public function edit(Request $request, $id = null)
    {
        $Coupon = null;
        if (!$id) {
            // 新規登録
            $Coupon = new CustomerCoupon();
            $Coupon->setVisible(true);
        } else {
            // 更新
            $Coupon = $this->couponRepository->find($id);
            if (!$Coupon) {
                $this->addError('plugin_coupon.admin.notfound', 'admin');

                return $this->redirectToRoute('plugin_customer_coupon_list');
            }
        }

        $form = $this->formFactory->createBuilder(CustomerCouponType::class, $Coupon)->getForm();

        // クーポンコードの発行
        if (!$id) {
            $form->get('coupon_cd')->setData($this->customerCouponService->generateCouponCd());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Plugin\CustomerCoupon42\Entity\CustomerCoupon $CustomerCoupon */
            $CustomerCoupon = $form->getData();
            $this->entityManager->persist($CustomerCoupon);
            $this->entityManager->flush($CustomerCoupon);

            // 成功時のメッセージを登録する
            $this->addSuccess('plugin_customer_coupon.admin.regist.success', 'admin');

            return $this->redirectToRoute('plugin_customer_coupon_list');
        }

        return $this->renderRegistView([
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }

    /**
     * 編集画面用のrender
     *
     * @param array $parameters
     *
     * @return Response
     */
    protected function renderRegistView($parameters = [])
    {
        $viewParameters = [
        ];

        $viewParameters += $parameters;

        return $this->render('@CustomerCoupon42/admin/regist.twig', $viewParameters);
    }
}
