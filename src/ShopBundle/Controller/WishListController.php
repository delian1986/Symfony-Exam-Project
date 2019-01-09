<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Service\WishListServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package ShopBundle\Controller
 * @Route("wish-list")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class WishListController extends Controller
{
    /**
     * @var WishListServiceInterface
     */
    private $wishListService;

    public function __construct(WishListServiceInterface $wishListService)
    {
        $this->wishListService=$wishListService;
    }

    /**
     * @Route("/show", name="wish_list_show")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('user/wish_list_show.html.twig', [
            "wishList" => $this->getUser()->getWishList()]);
    }

    /**
     * @Route("/add/{id}",name="wish_list_add")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToWishList(Product $product)
    {
        $this->wishListService->addToWishList($product,$this->getUser());

        return $this->redirectToRoute('wish_list_show');
    }

    /**
     * @Route("/remove/{id}", name="wish_list_remove")
     *
     * @param $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromCartAction(Product $product)
    {
        $this->wishListService->removeFromWishList($product, $this->getUser());
        return $this->redirectToRoute('wish_list_show');
    }
}
