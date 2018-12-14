<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\InitialCash;
use ShopBundle\Form\AddInitialCashType;
use ShopBundle\Service\InitialCashServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InitialCashController
 * @package ShopBundle\Controller
 * @Route("initial-cash")
 * @Security("is_granted('ROLE_ADMIN')")
 *
 * Initial cash for new registered users. The default value of 0.00 is set by DataFixtures/AppFixtures.php
 */
class InitialCashController extends Controller
{
    /**
     * @var InitialCashServiceInterface
     */
    private $initialCashService;

    public function __construct(InitialCashServiceInterface $initialCashService)
    {
        $this->initialCashService = $initialCashService;
    }

    /**
     * @Route("/add",name="add_initial_cash")
     * @param Request $request
     * @Security("is_granted('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addInitialCashToUsers(Request $request)
    {
        $initialCash = $this->initialCashService->getOldInitialCashValue();
        $form = $this->createForm(AddInitialCashType::class, $initialCash);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->initialCashService->insertInitialCash($initialCash);

            return $this->redirectToRoute('add_initial_cash');
        }

        return $this->render('admin/initial_cash/add_cash.html.twig', ['form' => $form->createView()]);
    }
}
