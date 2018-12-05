<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Category;
use ShopBundle\Form\CategoryAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package ShopBundle\Controller
 * @Route("/category")
 */
class CategoryController extends Controller
{

    /**
     * Class CategoryController
     * @package ShopBundle\Controller
     * @Route("/category/add",name="category_add")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCategory(Request $request)
    {
        $category=new Category();
        $form=$this->createForm(CategoryAddType::class,$category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success',"{$category->getName()} added successfully!");
        }


        return $this->render('admin/category/add_category.html.twig',['form'=>$form->createView()]);
    }
}
