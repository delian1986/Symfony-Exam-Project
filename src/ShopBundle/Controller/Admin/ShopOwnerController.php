<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\ShopOwner;
use ShopBundle\Entity\User;
use ShopBundle\Form\ShopOwnerType;
use ShopBundle\Repository\ProductRepository;
use ShopBundle\Service\ShopOwnerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ShopOwnerController extends Controller
{

    /**
     * @var ShopOwnerServiceInterface
     */
    private $shopOwnerService;


    public function __construct(ShopOwnerServiceInterface $shopOwnerService)
    {
        $this->shopOwnerService = $shopOwnerService;
    }

    /**
     * @Route("/set-owner", name="set_owner")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function chooseShopOwnerAction(Request $request)
    {
        $currentOwner=$this->shopOwnerService->getOwner();
        // only current owner have access to change shop owner page
        if ($this->getUser() !== $currentOwner){
            $content=$this->render('exception/error403.html.twig');
            return new Response($content,403);
        }
        $owner = new ShopOwner();
        $form = $this->createForm(ShopOwnerType::class, $owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->shopOwnerService->changeShopOwner($owner);

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/set_owner.html.twig', ['form' => $form->createView()]);
    }
}
