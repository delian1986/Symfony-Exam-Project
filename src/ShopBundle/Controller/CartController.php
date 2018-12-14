<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Service\CartServiceInterface;
use ShopBundle\Service\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CartController
 * @package ShopBundle\Controller
 *
 * @Route("/cart")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class CartController extends Controller
{
    /**
     * @var CartServiceInterface
     */
    private $cartService;

    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    public function __construct(CartServiceInterface $cartService,OrderServiceInterface $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService=$orderService;
    }

    /**
     * @Route("/add/{id}", name="cart_add")
     *
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

    public function cartAdd(Product $product, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $quantity=$request->request->get('product_quantity');

        $this->cartService->addToCart($product, $user,$quantity);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/show", name="cart_show")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function cartShow()
    {


    }

    /**
     * @Route("/checkout", name="cart_checkout", methods={"POST"})
    )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cartCheckOut(Request $request)
    {
        $chosenProductsWithQuantity = $request->request->all();
        $user = $this->getUser();
        $products = $this->cartService->checkoutPreview($user, $chosenProductsWithQuantity);
        $totalPrice=$this->orderService->getOrderTotalPrice($products);

        return $this->render('product/checkout.html.twig', ['products' => $products, 'total'=>$totalPrice]);
    }


}
