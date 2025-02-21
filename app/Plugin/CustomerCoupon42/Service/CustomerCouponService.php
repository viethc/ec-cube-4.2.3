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

namespace Plugin\CustomerCoupon42\Service;

use Eccube\Entity\Order;
use Eccube\Entity\TaxRule;
use Eccube\Entity\Customer;
use Eccube\Service\TaxRuleService;
use Eccube\Repository\TaxRuleRepository;
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;
use Plugin\CustomerCoupon42\Entity\CustomerCouponOrder;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;
use Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class CustomerCouponService.
 */
class CustomerCouponService
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TaxRuleService
     */
    private $taxRuleService;

    /**
     * @var TaxRuleRepository
     */
    private $taxRuleRepository;

    /**
     * @var CustomerCouponRepository
     */
    private $customerCouponRepository;

    /**
     * @var CustomerCouponOrderRepository
     */
    private $customerCouponOrderRepository;

    /**
     * Summary of __construct
     * 
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker
     * @param \Eccube\Service\TaxRuleService $taxRuleService
     * @param \Eccube\Repository\TaxRuleRepository $taxRuleRepository
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponRepository $customerCouponRepository
     * @param \Plugin\CustomerCoupon42\Repository\CustomerCouponOrderRepository $customerCouponOrderRepository
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TaxRuleService $taxRuleService,
        TaxRuleRepository $taxRuleRepository,
        CustomerCouponRepository $customerCouponRepository,
        CustomerCouponOrderRepository $customerCouponOrderRepository
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->taxRuleService = $taxRuleService;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->customerCouponRepository = $customerCouponRepository;
        $this->customerCouponOrderRepository = $customerCouponOrderRepository;
    }

    /**
     * Tạo Coupon Code
     *
     * @param int $length
     *
     * @return string
     */
    public function generateCouponCd($length = 12)
    {
        $couponCd = substr(base_convert(md5(uniqid()), 16, 36), 0, $length);

        return $couponCd;
    }

    /**
     * Đăng ký phát hành Coupon cho khách hàng hoàn thành đơn hàng
     * 
     * @param \Eccube\Entity\Order $Order
     * @param \Eccube\Entity\Customer $Customer
     * @param \Plugin\CustomerCoupon42\Entity\CustomerCoupon $CustomerCoupon
     * @return void
     */
    public function registCustomerCouponOrder(
        Order $Order,
        Customer $Customer,
        CustomerCoupon $CustomerCoupon
    ) {
        $CustomerCouponOrder = new CustomerCouponOrder();
        $currentDateTime = new \DateTime();

        // Coupon có 30 ngày hiệu lực từ sau khi hoàn thành đơn hàng 1 ngày
        // TODO Số ngày hiệu lực nên có thể đăng ký khi tạo Customer Coupon
        $currentDateTime->setTime(0, 0, 0);
        $fromDate = (clone $currentDateTime)->add(new \DateInterval('P1D'));
        $toDate = (clone $currentDateTime)->add(new \DateInterval('P30D'));

        $CustomerCouponOrder->setCouponId($CustomerCoupon->getId());
        $CustomerCouponOrder->setCouponCd($CustomerCoupon->getCouponCd());
        $CustomerCouponOrder->setCouponName($CustomerCoupon->getCouponName());
        $CustomerCouponOrder->setCouponLowerLimit($CustomerCoupon->getCouponLowerLimit());
        $CustomerCouponOrder->setDiscountRate($CustomerCoupon->getDiscountRate());
        $CustomerCouponOrder->setBuyOrderId($Order->getId());
        $CustomerCouponOrder->setAvailableFromDate($fromDate);
        $CustomerCouponOrder->setAvailableToDate($toDate);
        $CustomerCouponOrder->setVisible(true);
        $CustomerCouponOrder->setOrderChangeStatus(true);

        // Trường hợp đã login, sử dụng user_id
        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $CustomerCouponOrder->setCustomerId($Customer->getId());
        } else {
            $CustomerCouponOrder->setCustomerEmail($Customer->getEmail());
        }

        // Thêm thông tin Coupon vào cuối Mail gửi khách hàng
        // TODO Mail đã được gửi sau khi hoàn thành đơn hàng, nên phần bên dưới cần xem lại
        $this->setOrderCompleteMailMessage($Order, $CustomerCoupon->getCouponCd(), $CustomerCoupon->getCouponName());

        $this->customerCouponOrderRepository->save($CustomerCouponOrder);

        // Update `Số lượt khả dụng còn lại` của Customer Coupon
        $CustomerCoupon->setCouponUseTime($CustomerCoupon->getCouponUseTime() > 0 ? $CustomerCoupon->getCouponUseTime() - 1 : 0);
        $this->customerCouponRepository->save($CustomerCoupon);
    }

    /**
     * Tính toán số tiền giảm
     * 
     * @param \Plugin\CustomerCoupon42\Entity\CustomerCoupon $CustomerCoupon
     * @param mixed $totalPrice
     * @return float|int
     */
    public function calcDiscount(CustomerCoupon $CustomerCoupon, $totalPrice)
    {
        $discount = 0;

        if ($CustomerCoupon) {
            /** @var TaxRule $DefaultTaxRule */
            $DefaultTaxRule = $this->taxRuleRepository->getByRule();

            $discount = $this->taxRuleService->calcTax(
                $totalPrice,
                $CustomerCoupon->getDiscountRate(),
                $DefaultTaxRule->getRoundingType()->getId(),
                $DefaultTaxRule->getTaxAdjust()
            );
        }

        return $discount;
    }

    /**
     * Thêm thông tin Customer Coupon vào nội dung mail gửi khách hàng
     * 
     * @param \Eccube\Entity\Order $Order
     * @param mixed $couponCd
     * @param mixed $couponName
     * @return void
     */
    public function setOrderCompleteMailMessage(Order $Order, $couponCd = null, $couponName = null)
    {
        $snippet = '***********************************************'.PHP_EOL;
        $snippet .= '　顧客のクーポン情報                            '.PHP_EOL;
        $snippet .= '***********************************************'.PHP_EOL;
        $snippet .= PHP_EOL;
        $snippet .= 'クーポンコード: ';

        $message = $Order->getCompleteMailMessage();
        if ($message) {
            $message = preg_replace('/'.preg_quote($snippet).'.*$/m', '', $message);
            $Order->setCompleteMailMessage($message ? trim($message) : null);
            $snippet = PHP_EOL.$snippet; // 行頭に改行コードを追加
        }

        if ($couponCd && $couponName) {
            $snippet .= $couponCd.' '.$couponName.PHP_EOL;
            $Order->appendCompleteMailMessage($snippet);
        }
    }
}
