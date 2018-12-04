<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\AdminHelper;
use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends Controller
{

    //initial cash for all users unless it is changed from admin panel
    const INITIAL_CASH = 0.00;

    /**
     * @Route("/register",name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            if (0 === $this->getCountOfRegisteredUsers()) {
                $this->setUpFirstUserRegister();
                $userRole = $roleRepository->findOneBy(['name' => 'ROLE_ADMIN']);
            } else {
                $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            }

            $initialCash = $this->getDoctrine()->getRepository(AdminHelper::class)
                ->getInitialCashValue();

            $user->setBalance($initialCash);
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
    private function getCountOfRegisteredUsers(): int
    {
        return count($this->getDoctrine()->getRepository(User::class)->findAll());
    }

    /**
     * This function handle
     * @return void
     */
    private function setUpFirstUserRegister(): void
    {
        // add default initial cash to new registered users if table is not empty
        $adminHelper = new AdminHelper();
        $adminHelper->setInitialCash(self::INITIAL_CASH);

        //if initial cash is empty
        if (0 === count($this->getDoctrine()->getRepository(AdminHelper::class)->findAll())) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($adminHelper);
            $em->flush();
        }

        //adding roles to role table if its empty
        if (0 === count($this->getDoctrine()->getRepository(Role::class)->findAll())) {
            $roles = [
                'ROLE_ADMIN',
                'ROLE_EDITOR',
                'ROLE_USER'
            ];

            $em = $this->getDoctrine()->getManager();
            for ($i = 0; $i < count($roles); $i++) {
                $role = new Role();
                $role->setName($roles[$i]);
                $em->persist($role);
                $em->flush();
            }
        }

    }
}
