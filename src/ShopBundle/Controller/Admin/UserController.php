<?php

namespace ShopBundle\Controller\Admin;

use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserAdminEditForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UsersController
 * @package ShoppingCartBundle\Controller
 *
 * @Route("/admin/users")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends Controller
{

    /**
     * @Route("/all", name="admin_users_all")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allUsersAction(Request $request, PaginatorInterface $paginator): \Symfony\Component\HttpFoundation\Response
    {
        $users = $paginator->paginate(
            $this->getDoctrine()->getRepository(User::class)
                ->findAll(),
            $request->query->getInt('page', 1),
            9
        );;

        return $this->render('admin/users/all.html.twig', [
            "users" => $users
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_users_delete")
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserAction(User $user): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (count($user->getOrders()) > 0) {
            $this->addFlash(
                "danger", "You cant delete user which has products! You can ban his account!");
        } elseIf ($this->getUser() === $user) {
            $this->addFlash(
                "danger", "You cant delete yourself!");
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->addFlash("success", "User with username {$user->getEmail()} deleted successfully!");
        }
        return $this->redirectToRoute('admin_users_all');
    }

    /**
     * @Route("/edit/{id}", name="admin_users_edit")
     *
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editUserAction(Request $request, User $user): \Symfony\Component\HttpFoundation\Response
    {
        $form = $this->createForm(UserAdminEditForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash("success", "User {$user->getFullName()} edited successfully!");

            return $this->redirectToRoute('admin_users_all');
        }

        return $this->render('admin/users/edit.html.twig', [
            "edit_form" => $form->createView()
        ]);
    }
}
