<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\ShopOwner;
use ShopBundle\Entity\User;
use ShopBundle\Form\ShopOwnerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ShopOwnerController extends Controller
{

    /**
     * @Route("/set-owner", name="set_owner")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function chooseShopOwnerAction(Request $request)
    {
        $owner = new ShopOwner();
        $form = $this->createForm(ShopOwnerType::class,$owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($owner);
            $em->flush();

        }

        return $this->render('admin/set_owner.html.twig', ['form' => $form->createView()]);
    }
}
