<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Product;
use ShopBundle\Service\CartServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function __construct(CartServiceInterface $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/add/{id}", name="cart_add")
     *
     * @param $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

    public function cartAdd(Product $product)
    {
        $user = $this->getUser();
        $this->cartService->addToCart($product, $user);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/show", name="cart_show")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function cartShow()
    {
        
    }
}
