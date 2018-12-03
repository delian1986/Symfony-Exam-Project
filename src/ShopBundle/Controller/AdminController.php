<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use ShopBundle\Entity\AdminHelper;
use ShopBundle\Entity\Product;
use ShopBundle\Form\AdminAddCashType;
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
    public function addInitialCashToUsers(Request $request){
        $initialCash=new AdminHelper();
        $form=$this->createForm(AdminAddCashType::class,$initialCash);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $oldInitialCash=$em->getRepository(AdminHelper::class)->getOldInitialCash();

            //remove old initial cash
            $em->remove($oldInitialCash);
            $em->flush();

            //set new initial cash
            $em->persist($initialCash);
            $em->flush();

            return $this->redirectToRoute('shop_admin_index');
        }


        return $this->render('admin/add_cash.html.twig',['form'=>$form->createView()]);
    }
}
