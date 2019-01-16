<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\OrdersProducts;
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

    public function __construct(CartServiceInterface $cartService, OrderServiceInterface $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    /**
     * @Route("/add/{id}", name="cart_add")
     *
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function cartAdd(Request $request, Product $product = null)
    {
        if (null === $product) {
            return $this->render('exception/error404.html.twig');
        }

        if (0 === $product->isListed()){
            return $this->render('exception/unlisted_product.html.twig');
        }

        /** @var User $user */
        $user = $this->getUser();
        $quantity = $request->request->get('product_quantity');

        if ($quantity<1||$quantity>$product->getQuantity()){
            $this->addFlash('danger','Invalid product quantity');
            return $this->redirectToRoute('product_details',['slug'=>$product->getSlug()]);
        }

        $this->cartService->addToCart($product, $user, $quantity);

        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/show", name="cart_show")
     */
    public function cartShow()
    {
        /** @var User $user */
        $user = $this->getUser();

        $openOrder = $this->cartService->itemsInCart($user);
        $total = null;

        if ($openOrder) {
            $total = $openOrder->getTotal();
        }

        return $this->render('user/cart.html.twig', ['order' => $openOrder, 'total' => $total]);
    }

    /**
     * @Route("/checkout", name="cart_checkout")
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function cartCheckOut()
    {
        $user = $this->getUser();

        if (false === $this->cartService->checkout($user)) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param OrdersProducts $product
     * @param Request $request
     * @Route("/edit-quantity/{id}", name="cart_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cartEditQuantity(Request $request,OrdersProducts $product=null)
    {
        if (null === $product) {
            return $this->render('exception/error404.html.twig');
        }

        $quantity = $request->request->get('product_quantity');
        $user = $this->getUser();
        $this->cartService->editItemQuantity($user, $product, $quantity);
        return $this->redirectToRoute('cart_show');
    }

    /**
     * @param OrdersProducts $product
     * @Route("/remove/{id}",name="cart_remove")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeProductFromCart(OrdersProducts $product=null)
    {
        if (null === $product) {
            return $this->render('exception/error404.html.twig');
        }

        $user = $this->getUser();
        $this->cartService->removeFromCart($user, $product);

        return $this->redirectToRoute('cart_show');
    }
}
