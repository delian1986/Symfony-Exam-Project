<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserType;

use ShopBundle\Service\InitialCashServiceInterface;
use ShopBundle\Service\RoleServiceInterface;
use ShopBundle\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;
    private $initialCashService;
    private $roleService;

    public function __construct(UserServiceInterface $userService,
                                InitialCashServiceInterface $initialCashService,
                                RoleServiceInterface $roleService)
    {
        $this->userService = $userService;
        $this->initialCashService= $initialCashService;
        $this->roleService=$roleService;
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
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            //   If there is no users in DB first one should be ADMIN all others are USERS
            if ($this->userService->isFirstRegistration()) {
                $userRole = $this->roleService->getRole(['name' => 'ROLE_ADMIN']);
            } else {
                $userRole = $this->roleService->getRole(['name' => 'ROLE_USER']);
            }


            $initialCash = $this->initialCashService->getInitialCashValue();

            $user->setBalance($initialCash);
            $user->addRole($userRole);

            $this->userService->registerUser($user);

            return $this->redirectToRoute('security_login');
        }

        return $this->redirectToRoute('user_register');
    }
}
