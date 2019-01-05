<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Promotion;
use ShopBundle\Form\PromotionType;
use ShopBundle\Service\PromotionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PromotionController
 * @package ShopBundle\Controller\Admin
 *
 * @Route("/admin")
 * @Security("is_granted('ROLE_EDITOR')")
 */
class PromotionController extends Controller
{
    /**
     * @var PromotionServiceInterface
     */
    private $promotionService;

    public function __construct(PromotionServiceInterface $promotionService)
    {
        $this->promotionService=$promotionService;
    }

    /**
     * @Route("/promotion/add", name="admin_promotion_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPromotion(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class,$promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->promotionService->addPromotion($promotion);

        }

        return $this->render('admin/promotions/add_promotion.html.twig',['form'=>$form->createView()]);
    }
}
