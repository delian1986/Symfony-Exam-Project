<?php

namespace ShopBundle\Controller;

use ShopBundle\Service\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrdersController
 * @package ShopBundle\Controller
 * @Route("/order")
 */
class OrderController extends Controller
{
    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService=$orderService;
    }

//    /**
//     * @Route("/confirm", name="cart_confirm_order", methods={"POST"})
//     * )
//     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     */
//    public function cartConfirmOrder(Request $request){
//        $chosenProductsWithQuantity = $request->request->all();
//        $user= $this->getUser();
//        $this->orderService->confirmOrder($user, $chosenProductsWithQuantity);
//
//       return $this->redirectToRoute('homepage');
//    }
}
