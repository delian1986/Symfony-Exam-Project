<?php

namespace ShopBundle\Controller\Admin;

use Doctrine\DBAL\DBALException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Product;
use ShopBundle\Form\ProductType;
use ShopBundle\Service\ProductServiceInterface;
use ShopBundle\Service\ShopOwnerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package ShopBundle\Controller
 * @Route("admin/product")
 * @Security("is_granted('ROLE_EDITOR')")
 */
class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /** @var ShopOwnerServiceInterface */
    private $shopOwnerService;

    public function __construct(ProductServiceInterface $productService, ShopOwnerServiceInterface $shopOwnerService)
    {
        $this->productService = $productService;
        $this->shopOwnerService = $shopOwnerService;
    }

    /**
     * @Route("/add", name="product_add")
     * @Security("is_granted('ROLE_EDITOR')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function productAdd(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $owner = $this->shopOwnerService->getOwner();
            $product->setOwner($owner);

            $fileName = $this->uploadPicture($form->get('image')->getData());
            $product->setImage($fileName);
            $product->setSoldTimes(0);

            $this->productService->saveProduct($product);

            return $this->redirectToRoute('admin_products_all');
        }
        return $this->render('admin/products/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/all/", name="admin_products_all")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allProducts(Request $request, PaginatorInterface $paginator)
    {
        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)
                ->findAllByQueryBuilder(),
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('admin/products/all.html.twig', ['products' => $products]);

    }

    /**
     * @Route("/products/delete/{slug}", name="admin_products_delete")
     *
     * @param Product $product
     * @return Response
     */
    public function deleteProductsAction(Product $product): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        } catch (DBALException $exception) {
            $this->addFlash('danger', 'You cannot delete product that is already purchased! You can only unlist it from the shop!');
            return $this->redirectToRoute('admin_products_all');
        }

        $this->addFlash("success", "Product {$product->getName()} was deleted successfully!");
        return $this->redirectToRoute('admin_products_all');
    }

    /**
     * @param Product $product
     * @param Request $request
     * @Route("/edit/{slug}", name="admin_product_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editProduct(Product $product, Request $request)
    {
        $this->deleteOldPicture($product->getId());
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileName = $this->uploadPicture($form->get('image')->getData());
            $product->setImage($fileName);
            $this->productService->saveProduct($product);

            if ($product->isListed()) {
                $this->productService->notifyUsersWithProductInTheirWishList($product->getUserWishList(), $product);
            }

            return $this->redirectToRoute('admin_products_all');

        }

        return $this->render('admin/products/create.html.twig', ['form' => $form->createView()]);
    }

    private function uploadPicture($image)
    {
        $fileName = md5(uniqid()) . '.' . $image->guessExtension();

        try {
            $image->move($this->getParameter('products_directory'),
                $fileName);
        } catch (FileException $ex) {

        }

        return $fileName;
    }

    private function deleteOldPicture(int $productId)
    {
        $dir = $this->getParameter('products_directory');
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['id' => $productId]);
        $oldImage = $dir . $product->getImage();
        if (file_exists($oldImage)) {
            unlink($oldImage);
        }
    }


}
