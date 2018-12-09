<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Product;
use ShopBundle\Form\ProductType;
use ShopBundle\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package ShopBundle\Controller
 * @Route("admin/product")
 * @IsGranted("ROLE_ADMIN")

 */
class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService=$productService;
    }

    /**
     * @Route("/add", name="product_add")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function productAdd(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $owner = $this->getUser();
            $product->setOwner($owner);

            $this->productService->insertProduct($product);

            return $this->redirectToRoute("admin_index");
        }
        return $this->render('admin/products/create.html.twig', ['form' => $form->createView()]);
    }
}
