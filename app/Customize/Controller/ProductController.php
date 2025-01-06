<?php

namespace Customize\Controller;

use Eccube\Entity\Product;
use Eccube\Service\CartService;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\BaseInfoRepository;
use Customize\Repository\SupplierRepository;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Eccube\Repository\Master\ProductListMaxRepository;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Eccube\Controller\ProductController as BaseProductController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ProductController extends BaseProductController
{
    /**
     * @var SupplierRepository
     */
    protected $supplierRepository;

    /**
     * ProductController constructor.
     *
     * @param PurchaseFlow $cartPurchaseFlow
     * @param CustomerFavoriteProductRepository $customerFavoriteProductRepository
     * @param CartService $cartService
     * @param ProductRepository $productRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param AuthenticationUtils $helper
     * @param ProductListMaxRepository $productListMaxRepository
     * @param SupplierRepository $supplierRepository
     */
    public function __construct(
        PurchaseFlow $cartPurchaseFlow,
        CustomerFavoriteProductRepository $customerFavoriteProductRepository,
        CartService $cartService,
        ProductRepository $productRepository,
        BaseInfoRepository $baseInfoRepository,
        AuthenticationUtils $helper,
        ProductListMaxRepository $productListMaxRepository,
        SupplierRepository $supplierRepository
    ) {
        parent::__construct(
            $cartPurchaseFlow,
            $customerFavoriteProductRepository,
            $cartService,
            $productRepository,
            $baseInfoRepository,
            $helper,
            $productListMaxRepository
        );

        $this->supplierRepository = $supplierRepository;
    }

    /**
     * 商品詳細画面.
     *
     * @Route("/products/detail/{id}", name="product_detail", methods={"GET"}, requirements={"id" = "\d+"})
     * @Template("Product/detail.twig")
     * @ParamConverter("Product", options={"repository_method" = "findWithSortedClassCategories"})
     *
     * @param Request $request
     * @param Product $Product
     *
     * @return array
     */
    public function detail(Request $request, Product $Product): array
    {
        $response = parent::detail($request, $Product);
        $suppliers = $this->supplierRepository->findAllOrderedByName();

        return array_merge($response, [
            'suppliers' => $suppliers
        ]); 
    }
}
