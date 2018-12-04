<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\InitialCash;
use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserType;

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

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/register",name="user_register",  methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register()
    {
        if ($this->get("security.authorization_checker")->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("homepage");
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        return $this->render('user/register.html.twig',['form'=>$form->createView()]);
    }

    /**

     * @Route("/register", name="user_register_proceed", methods={"POST"})
     * @param Request $request
     * @throws \Doctrine\ORM\NonUniqueResultException
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

            $roleRepository = $this->getDoctrine()->getRepository(Role::class);

            //   If there is no users in DB first one should be ADMIN all others are USERS
            if ($this->userService->isFirstRegistration()) {
                $userRole = $roleRepository->findOneBy(['name' => 'ROLE_ADMIN']);
            } else {
                $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            }

            $initialCash = $this->getDoctrine()->getRepository(InitialCash::class)
                ->getInitialCashValue();

            $user->setBalance($initialCash);
            $user->addRole($userRole);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success","You have registered successfully!");

            return $this->redirectToRoute('security_login');
        }
    }
}
