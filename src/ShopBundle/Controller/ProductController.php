<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\Review;
use ShopBundle\Form\ReviewAddType;
use ShopBundle\Service\ReviewServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package ShopBundle\Controller
 * @Route("product")
 */
class ProductController extends Controller
{
    /**
     * @var ReviewServiceInterface
     */
    private $reviewService;

    public function __construct(ReviewServiceInterface $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * @Route("/details/{slug}", name="product_details")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productDetails(Product $product = null)
    {
        if (null === $product) {
            return $this->render('exception/error404.html.twig');
        }
        if (false === $product->isListed()) {
            return $this->render('exception/error404.html.twig');
        }

        return $this->render('product/details.html.twig', [
            'product' => $product,
            "review_add" => $this->createForm(ReviewAddType::class)->createView()
        ]);
    }

    /**
     * @Route("/products/{slug}/review/add", name="product_add_review", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addReview(Request $request, Product $product)
    {
        $review = $this->reviewService->getReviewByUserAndProduct(
            $this->getUser(), $product
        );

        if ($review) {
            $this->addFlash("danger", "You already has reviewed this product!");
            return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);
        }
        $form = $this->createForm(ReviewAddType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Review $review */
            $review = $form->getData();
            $review->setAuthor($this->getUser());
            $review->setProduct($product);
            $this->reviewService->addReview($review);

            return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);
        }

        return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);

    }

    /**
     * @param Review $review
     * @Route("/reviews/{id}/delete", name="product_delete_review", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteReview(Review $review)
    {
        $product = $review->getProduct();
        if ($review->getAuthor() !== $this->getUser()) {
            $this->addFlash("danger", "You cannot delete this review!");
            return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);
        }

        $this->reviewService->deleteReview($review);

        return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);

    }

    /**
     * @param Review $review
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/reviews/{id}/edit", name="product_edit_review")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editReview(Review $review, Request $request)
    {
        $product = $review->getProduct();

        if ($review->getAuthor() !== $this->getUser()) {
            $this->addFlash("danger", "You cannot edit this review!");
            return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);
        }

        $form = $this->createForm(ReviewAddType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->reviewService->addReview($review);

            return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);

        }

        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('danger', $error->getMessage());
        }

        return $this->render('product/edit_review.html.twig', [
            'product' => $product,
            'review' => $review,
            "review_add" => $form->createView()
        ]);
    }
}
