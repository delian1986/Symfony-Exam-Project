<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserProfileEditForm;
use ShopBundle\Service\CartServiceInterface;
use ShopBundle\Service\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package ShopBundle\Controller
 * @Route("user")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")

 */
class UserController extends Controller
{
    /** @var CartServiceInterface */
    private $cartService;
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    public function __construct(ProductServiceInterface $productService,
                                CartServiceInterface $cartService)
    {
        $this->productService=$productService;
        $this->cartService=$cartService;
    }

    /**
     * @Route("/orders",name="my_orders")
     */
    public function myOrdersAction()
    {
        /** @var User $user */
        $user=$this->getUser();
        $orders=$user->getOrders();


        return $this->render('user/orders.html.twig',['orders'=>$orders]);
    }

    /**
     * @Route("/products",name="my_products")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myProductsAction(){
        /** @var User $user */
        $user= $this->getUser();
        $myProducts=$user->getMyProducts();

        return $this->render('user/products.html.twig',['products'=>$myProducts]);
    }

    /**
     * @Route("/profile", name="user_profile")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(): \Symfony\Component\HttpFoundation\Response
    {
        $user = $this->getUser();

        return $this->render("user/profile.html.twig", [
            "user" => $user
        ]);
    }

    /**
     * @Route("/profile/edit", name="user_profile_edit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileEditAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileEditForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash("success", "Profile edited successfully!");
            return $this->redirectToRoute("user_profile");
        }

        return $this->render('user/edit.html.twig', [
            "edit_form" => $form->createView()
        ]);
    }


}
