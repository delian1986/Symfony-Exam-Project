<?php

namespace ShopBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use ShopBundle\Entity\Promotion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PromotionController
 * @package ShopBundle\Controller
 * @Route("/promotions")
 */
class PromotionController extends Controller
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/all", name="promotions_all")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allPromotions(Request $request)
    {
        $promotions = $this->paginator->paginate(
            $this->getDoctrine()->getRepository(Promotion::class)
                ->findAllAvailableByQueryBuilder(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('promotions/all.html.twig', [
            'promotions' => $promotions
        ]);
    }

    /**
     * @Route("/{id}/products/", name="promotion_show_products")
     *
     * @param Request $request
     * @param Promotion $promotion
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function showPromotionProducts(Request $request, Promotion $promotion)
    {
        if (!$promotion->isActive()) {
            $this->addFlash('danger','This promotion is not active at this time!');
            return $this->redirectToRoute('promotions_all');
        }

        $products = $this->paginator->paginate(
            $promotion->getProducts(),
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('promotions/show_promotion_products.html.twig', [
            'products' => $products
        ]);
    }
}
