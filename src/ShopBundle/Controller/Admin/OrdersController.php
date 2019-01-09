<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use ShopBundle\Entity\Order;
use ShopBundle\Form\DeclineOrderType;
use ShopBundle\Service\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        $this->orderService = $orderService;
    }

    /**
     * @Route("/action/{id}",name="admin_order_take_action")
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderTakeAction(Order $order)
    {
        return $this->render('admin/orders/take_action.html.twig', ['order' => $order]);
    }


    /**
     * @Route("/complete-order/{id}",name="admin_order_complete")
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function completeOrder(Order $order)
    {
        if (!$this->orderService->completeOrder($order)) {
            return $this->redirectToRoute('admin_order_decline', ['id' => $order->getId()]);
        }

        return $this->redirectToRoute("admin_show_all_orders", ['param' => 'all']);
    }

    /**
     * @Route("/decline-order/{id}",name="admin_order_decline")
     * @param Order $order
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function declineOrder(Order $order, Request $request)
    {
        $form = $this->createForm(DeclineOrderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reason = $form['Reason']->getData();
            $this->orderService->declineOrder($order, $reason);
            return $this->redirectToRoute("admin_show_all_orders", ['param' => 'all']);
        }

        return $this->render('admin/orders/decline_order.html.twig', ['order' => $order, 'form' => $form->createView()]);

    }

    /**
     * @Route("/{param}",name="admin_show_all_orders")
     * @param string $param
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllOrdersAction(string $param)
    {
        $orders = null;
        $label = ucfirst($param);

        switch ($param) {
            case 'all':
                $orders = $this->orderService->findAllOrders();
                break;
            default:
                $orders = $this->orderService->allOrdersByStatusName($param);
                break;
        }

        return $this->render('admin/orders/orders.html.twig', ['label' => $label, 'orders' => $orders]);
    }
}
