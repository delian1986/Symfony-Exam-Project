<?php

namespace ShopBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use ShopBundle\Entity\Category;
use ShopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller
{
    public function sidebarCategoriesAction(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('_sidebar_categories.html.twig', [
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/categories/{slug}", name="show_products_by_category")

     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function showCategoryAction(Request $request, Category $category, PaginatorInterface $paginator): Response
    {
        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)
                ->findAllbyCategoryQueryBuilder($category),
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('ShoppingCartBundle:categories:category.html.twig', [
            'products' => $products,
            'category' => $category
        ]);
    }
}
