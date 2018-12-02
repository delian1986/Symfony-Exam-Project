<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends Controller
{
    /**
     * @Route("/register",name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $roleRepository = $this->getDoctrine()->getRepository(Role::class);

         //   If there is no users in DB first one should be ADMIN all others are USERS
            if (0=== $this->getCountOfRegisteredUsers()) {
                $userRole = $roleRepository->findOneBy(['name' => 'ROLE_ADMIN']);
            } else {
                $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            }

            $user->addRole($userRole);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('security_login');
        }

        return $this->render('register/register.html.twig');
    }

    /**
     * @return int
     */
    private function getCountOfRegisteredUsers()
    {
        return count($this->getDoctrine()->getRepository(User::class)->findAll());
    }
}
