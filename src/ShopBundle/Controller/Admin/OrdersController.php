<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use ShopBundle\Entity\Order;
use ShopBundle\Service\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package ShopBundle\Controller
 * @Route("admin/orders")
 * @IsGranted("ROLE_ADMIN")
 */
class OrdersController extends Controller
{
    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService=$orderService;
    }

    /**
     * @Route("/all",name="admin_show_all_orders")
     */
    public function showAllOrdersAction()
    {
        $orders=$this->orderService->allOrders();

        return $this->render('admin/orders.html.twig',['orders'=>$orders]);
    }

    /**
     * @Route("/complete-order/{id}",name="admin_order_complete")
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function completeOrder(Order $order){
        $this->orderService->completeOrder($order);

        return $this->redirectToRoute('admin_show_all_orders');
    }
}
