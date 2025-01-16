<?php

namespace Plugin\CustomerCoupon42;

use Eccube\Entity\Order;
use Eccube\Event\TemplateEvent;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Plugin\CustomerCoupon42\Entity\CustomerCoupon;
use Plugin\CustomerCoupon42\Entity\CustomerCouponOrder;
use Symfony\Component\Workflow\Event\Event as CompletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Plugin\CustomerCoupon42\Repository\CustomerCouponRepository;

class Event implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CustomerCouponRepository
     */
    private $customerCouponRepository;

    /**
     * Event constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CustomerCouponRepository $customerCouponRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CustomerCouponRepository $customerCouponRepository
    ) {
        $this->entityManager = $entityManager;
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
            $event->addSnippet('@CustomerCoupon42/default/customer_coupon_shopping_index.twig');
        } else {
            $event->addSnippet('@CustomerCoupon42/default/customer_coupon_shopping_confirm.twig');
        }
    }

    public function onOrderStateCompleted(CompletedEvent $event): void
    {
        /** @var $context OrderStateMachineContext */
        $context = $event->getSubject();
        /** @var Order $Order */
        $Order = $context->getOrder();
        /** @var Customer $Customer */
        $Customer = $Order->getCustomer();

        $totalPrice = $Order->getSubtotal();

        /**
         * @var CustomerCoupon $CurrentCoupon
         */
        $CurrentCoupon = $this->customerCouponRepository->findOneActiveCoupon($totalPrice);

        if ($CurrentCoupon) {
            $currenDateTime = new \DateTime();
            $fromDate = $currenDateTime->add(new \DateInterval('P1D'));
            $toDate = $currenDateTime->add(new \DateInterval('P30D'));

            // 時分秒を0に設定する
            $currenDateTime->setTime(0, 0, 0);

            /** @var CustomerCouponOrder $CustomerCouponOrder */
            $CustomerCouponOrder = new CustomerCouponOrder();
            $CustomerCouponOrder->setCouponId($CurrentCoupon->getId());
            $CustomerCouponOrder->setCouponCd($CurrentCoupon->getCouponCd());
            $CustomerCouponOrder->setCouponName($CurrentCoupon->getCouponName());
            $CustomerCouponOrder->setCustomerId($Customer->getId());
            $CustomerCouponOrder->setEmail($Customer->getEmail());
            $CustomerCouponOrder->setOrderId($Order->getId());
            $CustomerCouponOrder->setAvailableFromDate($fromDate);
            $CustomerCouponOrder->setAvailableToDate($toDate);
            $CustomerCouponOrder->setVisible(true);

            $this->entityManager->persist($CustomerCouponOrder);
            $this->entityManager->flush();
        }
    }
}
