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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

    public function cartAdd(Product $product, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $quantity = $request->request->get('product_quantity');

        $this->cartService->addToCart($product, $user, $quantity);

        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/show", name="cart_show")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function cartShow()
    {
        /** @var User $user */
        $user = $this->getUser();
        $openOrder = $this->cartService->itemsInCart($user);
        $total = null;

        if ($openOrder){
            $total=$openOrder->getTotal();
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
        if (false===$this->cartService->checkout($user)) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param OrdersProducts $product
     * @param Request $request
     * @Route("/edit-quantity/{id}", name="cart_edit")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cartEditQuantity(OrdersProducts $product, Request $request)
    {
        $quantity = $request->request->get('product_quantity');
        $user = $this->getUser();
        $this->cartService->editItemQuantity($user, $product, $quantity);
        return $this->redirectToRoute('cart_show');
    }

    /**
     * @param OrdersProducts $product
     * @Route("/remove/{id}",name="cart_remove")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeProductFromCart(OrdersProducts $product)
    {
        $user = $this->getUser();
        $this->cartService->removeFromCart($user, $product);

        return $this->redirectToRoute('cart_show');
    }


}
