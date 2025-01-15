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

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Plugin\CustomerCoupon42\Service\CustomerCouponService;
use Plugin\CustomerCoupon42\Form\Type\Admin\CustomerCouponType;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;

/**
 * Class CustomerCouponController
 */
class CustomerCouponController extends AbstractController
{
    /**
     * @var CustomerCouponRepository
     */
    private $customerCouponReposity;

    /**
     * @var CustomerCouponService
     */
    private $customerCouponService;

    /**
     * CustomerCouponController constructor.
     *
     * @param CustomerCouponRepository $customerCouponReposity
     */
    public function __construct(CustomerCouponRepository $customerCouponReposity, CustomerCouponService $customerCouponService)
    {
        $this->customerCouponReposity = $customerCouponReposity;
        $this->customerCouponService = $customerCouponService;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @Route("/%eccube_admin_route%/plugin/customer-coupon", name="plugin_customer_coupon_list")
     * @Template("@CustomerCoupon42/admin/coupon-index.twig")
     */
    public function index(Request $request)
    {
        $coupons = $this->customerCouponReposity->findBy(
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
        $CustomerCoupon = null;
        if (!$id) {
            // 新規登録
            $CustomerCoupon = new CustomerCoupon();
            $CustomerCoupon->setEnableFlag(Constant::ENABLED);
            $CustomerCoupon->setVisible(true);
        } else {
            // 更新
            $CustomerCoupon = $this->customerCouponReposity->find($id);
            if (!$CustomerCoupon) {
                $this->addError('plugin_coupon.admin.notfound', 'admin');

                return $this->redirectToRoute('plugin_customer_coupon_list');
            }
        }

        $form = $this->formFactory->createBuilder(CustomerCouponType::class, $CustomerCoupon)->getForm();

        // クーポンコードの発行
        if (!$id) {
            $form->get('coupon_cd')->setData($this->customerCouponService->generateCouponCd());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Plugin\CustomerCoupon42\Entity\CustomerCoupon $CustomerCoupon */
            $CustomerCoupon = $form->getData();
            $oldReleaseNumber = $request->get('coupon_release_old');
            if (is_null($CustomerCoupon->getCouponUseTime())) {
                $CustomerCoupon->setCouponUseTime($CustomerCoupon->getCouponRelease());
            } else {
                if ($CustomerCoupon->getCouponRelease() != $oldReleaseNumber) {
                    $CustomerCoupon->setCouponUseTime($CustomerCoupon->getCouponRelease());
                }
            }
            $this->entityManager->persist($CustomerCoupon);
            $this->entityManager->flush();

            if (!$id) {
                // 成功時のメッセージを登録する
                $this->addSuccess('plugin_customer_coupon.admin.regist.success', 'admin');
            } else {
                // 成功時のメッセージを更新する
                $this->addSuccess('plugin_customer_coupon.admin.update.success', 'admin');
            }

            return $this->redirectToRoute('plugin_customer_coupon_list');
        }

        return $this->renderRegistView([
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }

    /**
     * クーポンの有効/無効化
     *
     * @param Request $request
     * @param CustomerCoupon  $Coupon
     *
     * @return RedirectResponse
     * @Route("/%eccube_admin_route%/plugin/customer-coupon/{id}/enable", name="plugin_customer_coupon_enable", requirements={"id" = "\d+"}, methods={"put"})
     * @ParamConverter("CustomerCoupon")
     */
    public function enable(Request $request, CustomerCoupon $Coupon)
    {
        $this->isTokenValid();
        $this->customerCouponReposity->enableCoupon($Coupon);
        $this->addSuccess('plugin_customer_coupon.admin.enable.success', 'admin');
        log_info('Change status a coupon with ', ['ID' => $Coupon->getId()]);

        return $this->redirectToRoute('plugin_customer_coupon_list');
    }

    /**
     * クーポンの削除
     *
     * @param Request $request
     * @param CustomerCoupon  $Coupon
     *
     * @return RedirectResponse
     * @Route("/%eccube_admin_route%/plugin/customer-coupon/{id}/delete", name="plugin_customer_coupon_delete", requirements={"id" = "\d+"}, methods={"delete"})
     * @ParamConverter("CustomerCoupon")
     */
    public function delete(Request $request, CustomerCoupon $Coupon)
    {
        $this->isTokenValid();
        $this->customerCouponReposity->deleteCoupon($Coupon);
        $this->addSuccess('plugin_customer_coupon.admin.delete.success', 'admin');
        log_info('Delete a coupon with ', ['ID' => $Coupon->getId()]);

        return $this->redirectToRoute('plugin_customer_coupon_list');
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

        return $this->render('@CustomerCoupon42/admin/coupon-regist.twig', $viewParameters);
    }
}
