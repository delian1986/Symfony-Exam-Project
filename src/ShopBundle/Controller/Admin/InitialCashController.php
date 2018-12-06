<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\InitialCash;
use ShopBundle\Form\AddInitialCashType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InitialCashController
 * @package ShopBundle\Controller
 * @Route("initial-cash")
 *
 * Initial cash for new registered users. The default value of 0.00 is set by DataFixtures/AppFixtures.php
 */
class InitialCashController extends Controller
{
    /**
     * @Route("/add",name="add_initial_cash")
     * @param Request $request
     * @Security("is_granted('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addInitialCashToUsers(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $initialCash = $em->getRepository(InitialCash::class)->getOldInitialCash();
        $form = $this->createForm(AddInitialCashType::class, $initialCash);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $initialCashRows = count($em->getRepository(InitialCash::class)->findAll());

            if ($initialCashRows === 1) {

                //remove old initial cash value
                $em->remove($initialCash);
                $em->flush();

                //set new initial cash value
                $em->persist($initialCash);
                $em->flush();

                return $this->redirectToRoute('add_initial_cash');
            }
        }

        return $this->render('admin/initial_cash/add_cash.html.twig', ['form' => $form->createView()]);
    }
}
