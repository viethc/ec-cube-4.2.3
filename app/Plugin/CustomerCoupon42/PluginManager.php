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

namespace Plugin\CustomerCoupon42;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Layout;
use Eccube\Entity\Page;
use Eccube\Entity\PageLayout;
use Eccube\Plugin\AbstractPluginManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PluginManager.
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function enable(array $meta, ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();
        $PageLayout = $entityManager->getRepository(Page::class)->findOneBy(['url' => 'plugin_customer_coupon_shopping']);
        if (is_null($PageLayout)) {
            // pagelayoutの作成
            $this->createPageLayout($container);
        }
    }

    /**
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function disable(array $meta, ContainerInterface $container)
    {
        // pagelayoutの削除
        $this->removePageLayout($container);
    }

    /**
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function update(array $meta, ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();
        $PageLayout = $entityManager->getRepository(Page::class)->findOneBy(['url' => 'plugin_customer_coupon_shopping']);
        if (is_null($PageLayout)) {
            // pagelayoutの作成
            $this->createPageLayout($container);
        }
    }

    /**
     * @param ContainerInterface $container
     */
    private function createPageLayout(ContainerInterface $container)
    {
        // ページレイアウトにプラグイン使用時の値を代入
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();
        /** @var \Eccube\Entity\Page $Page */
        $Page = $entityManager->getRepository(Page::class)->newPage();
        $Page->setEditType(Page::EDIT_TYPE_DEFAULT);
        $Page->setName('マイページ/マイクーポン');
        $Page->setUrl('plugin_customer_coupon_shopping');
        $Page->setFileName('CustomerCoupon42/Resource/template/default/mypage_mycoupon');
        $Page->setMetaRobots('noindex');

        // DB登録
        $entityManager->persist($Page);
        $entityManager->flush($Page);

        $Layout = $entityManager->getRepository(Layout::class)->find(Layout::DEFAULT_LAYOUT_UNDERLAYER_PAGE);
        $PageLayout = new PageLayout();
        $PageLayout->setPage($Page)
            ->setPageId($Page->getId())
            ->setLayout($Layout)
            ->setLayoutId($Layout->getId())
            ->setSortNo(0);

        $entityManager->persist($PageLayout);
        $entityManager->flush($PageLayout);
    }

    /**
     * クーポン用ページレイアウトを削除.
     *
     * @param ContainerInterface $container
     */
    private function removePageLayout(ContainerInterface $container)
    {
        // ページ情報の削除
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();
        $Page =  $entityManager->getRepository(Page::class)->findOneBy(['url' => 'plugin_customer_coupon_shopping']);
        if ($Page) {
            $Layout = $entityManager->getRepository(Layout::class)->find(Layout::DEFAULT_LAYOUT_UNDERLAYER_PAGE);
            $PageLayout = $entityManager->getRepository(PageLayout::class)->findOneBy(['Page' => $Page, 'Layout' => $Layout]);
            // Blockの削除
            $entityManager->remove($PageLayout);
            $entityManager->remove($Page);
            $entityManager->flush();
        }
    }
}
