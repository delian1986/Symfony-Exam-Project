<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Role;
use ShopBundle\Entity\ShopOwner;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserType;

use ShopBundle\Service\InitialCashServiceInterface;
use ShopBundle\Service\RoleServiceInterface;
use ShopBundle\Service\ShopOwnerService;
use ShopBundle\Service\ShopOwnerServiceInterface;
use ShopBundle\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends Controller
{
    private const STARTING_MONEY_VALUE = 0.00;

    /** @var UserServiceInterface */
    private $userService;

    /** @var InitialCashServiceInterface */
    private $initialCashService;

    /** @var RoleServiceInterface */
    private $roleService;

    /** @var ShopOwnerServiceInterface */
    private $shopOwnerService;

    public function __construct(UserServiceInterface $userService,
                                InitialCashServiceInterface $initialCashService,
                                RoleServiceInterface $roleService,
                                ShopOwnerServiceInterface $shopOwnerService)
    {
        $this->userService = $userService;
        $this->initialCashService = $initialCashService;
        $this->roleService = $roleService;
        $this->shopOwnerService = $shopOwnerService;
    }

    /**
     * @Route("/register",name="user_register",  methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register()
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/register", name="user_register_proceed", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function proceedRegister(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            if ($this->userService->findByUsername($user->getUsername())) {
                $this->addFlash('danger', "User already registered with {$user->getUsername()}");
                return $this->redirectToRoute('user_register');
            }

            //first user registration sets shop owner and admin to that user
            $isThisFirstRegister = false;
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            //  If there are no users in DB first one should be ADMIN all others are USERS
            if ($this->userService->isFirstRegistration()) {
                $isThisFirstRegister = true;
                $userRole = $this->roleService->getRole(['name' => 'ROLE_ADMIN']);
            } else {
                $userRole = $this->roleService->getRole(['name' => 'ROLE_USER']);
            }

            $initialCash = $this->initialCashService->getInitialCashValue();

            $user->setBalance($initialCash);
            $user->addRole($userRole);
            $user->setMoneySpent(self::STARTING_MONEY_VALUE);
            $user->setMoneyReceived(self::STARTING_MONEY_VALUE);

            $this->userService->saveUser($user);

            if ($isThisFirstRegister) {
                $owner = new ShopOwner();
                $owner->setShopOwner($user);
                $this->shopOwnerService->saveShopOwner($owner);
            }

            return $this->redirectToRoute('security_login');
        }
        var_dump($form->getErrors()->count()); exit();
        return $this->redirectToRoute('user_register');
    }
}
