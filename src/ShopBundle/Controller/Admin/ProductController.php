<?php

namespace ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Product;
use ShopBundle\Form\ProductType;
use ShopBundle\Repository\ProductRepository;
use ShopBundle\Service\ProductServiceInterface;
use ShopBundle\Service\ShopOwnerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
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
            $product->setIsListed(true);

            $this->productService->saveProduct($product);

            return $this->redirectToRoute("admin_index");
        }
        return $this->render('admin/products/create.html.twig', ['form' => $form->createView()]);
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
            return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);

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
