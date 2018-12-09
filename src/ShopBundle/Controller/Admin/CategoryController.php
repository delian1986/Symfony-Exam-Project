<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Category;
use ShopBundle\Form\CategoryAddType;
use ShopBundle\Service\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package ShopBundle\Controller
 * @Route("/category")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class CategoryController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService=$categoryService;
    }

    /**
     * Class CategoryController
     * @package ShopBundle\Controller
     * @Route("/add",name="category_add")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryAddType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->saveCategory($category);
        }

        return $this->render('admin/category/add_category.html.twig', ['form' => $form->createView()]);
    }
}
