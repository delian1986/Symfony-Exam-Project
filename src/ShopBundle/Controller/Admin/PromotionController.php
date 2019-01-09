<?php

namespace ShopBundle\Controller\Admin;

use Knp\Component\Pager\PaginatorInterface;
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
 * @Route("/admin/promotions")
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
     * @Route("/all", name="admin_promotions_all")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allPromotions(Request $request, PaginatorInterface $paginator)
    {
        $promotions = $paginator->paginate(
            $this->getDoctrine()->getRepository(Promotion::class)
                ->findAllByQueryBuilder(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/promotions/all.html.twig', [
            'promotions' => $promotions
        ]);
    }

    /**
     * @Route("/add", name="admin_promotion_add")
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

            return $this->redirectToRoute('admin_promotions_all');
        }

        return $this->render('admin/promotions/add_promotion.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/edit/{id}", name="admin_promotions_edit")
     *
     * @param Promotion $promotion
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editPromotion(Request $request, Promotion $promotion)
    {
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash("success", "Promotion edited successfully!");
            return $this->redirectToRoute("admin_promotions_all");
        }

        return $this->render("admin/promotions/add_promotion.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_promotions_delete")
     *
     * @param Promotion $promotion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePromotion(Promotion $promotion)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($promotion);
        $em->flush();

        $this->addFlash('success', 'Promotion deleted successfully');
        return $this->redirectToRoute('admin_promotions_all');
    }


}
