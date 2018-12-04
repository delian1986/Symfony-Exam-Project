<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use ShopBundle\Entity\InitialCash;
use ShopBundle\Entity\Product;
use ShopBundle\Form\AddInitialCashType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package ShopBundle\Controller
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/add-initial-cash",name="add_initial_cash")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addInitialCashToUsers(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $initialCash = $em->getRepository(InitialCash::class)->getOldInitialCash();
        $form = $this->createForm(AddInitialCashType::class, $initialCash);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldInitialCash = $em->getRepository(InitialCash::class)->getOldInitialCash();

            //remove old initial cash
            $em->remove($oldInitialCash);
            $em->flush();

            //set new initial cash
            $em->persist($initialCash);
            $em->flush();

            return $this->redirectToRoute('shop_admin_index');
        }


        return $this->render('admin/add_cash.html.twig', ['form' => $form->createView()]);
    }
}
