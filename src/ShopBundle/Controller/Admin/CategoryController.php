<?php

namespace ShopBundle\Controller\Admin;

use Knp\Component\Pager\PaginatorInterface;
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
 * @Route("admin/category")
 * @Security("is_granted('ROLE_EDITOR')")
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
     * @Route("/add",name="admin_category_add")
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
            return $this->redirectToRoute('admin_categories_all');
        }

        return $this->render('admin/category/add_category.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/all", name="admin_categories_all")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllCategories(Request $request, PaginatorInterface $paginator){
        $categories = $paginator->paginate(
            $this->getDoctrine()->getRepository(Category::class)
                ->findAllByQueryBuilder(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/category/all.html.twig', [
            'categories' => $categories
        ]);

    }

    /**
     * @Route("/edit/{slug}", name="admin_categories_edit")
     * @param Request $request
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editCategoryAction(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryAddType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', "Category {$category->getName()} was updated!");
            return $this->redirectToRoute('admin_categories_all');
        }

        return $this->render('admin/category/add_category.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_categories_delete")
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCategoryAction(Category $category): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($category->getProducts()->count() > 0) {
            $this->addFlash('danger', 'Category is not empty. You can\'t delete category with products!');
            return $this->redirectToRoute('admin_categories_all');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', "Category {$category->getName()} was deleted!");
        return $this->redirectToRoute('admin_categories_all');
    }
}
