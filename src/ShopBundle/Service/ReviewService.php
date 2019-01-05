<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Entity\Review;
use ShopBundle\Entity\User;
use ShopBundle\Repository\ReviewRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ReviewService implements ReviewServiceInterface
{
    /**
     * @var ReviewRepository
     */
    private $reviewRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        ReviewRepository $reviewRepository,
        FlashBagInterface $flashBag
    )
    {
        $this->reviewRepository = $reviewRepository;
        $this->flashBag = $flashBag;
    }

    public function getReviewByUserAndProduct(User $user, Product $product): ?Review
    {
        return $this->reviewRepository->findOneByProductAndUser($user, $product);
    }

    /**
     * @param Review $review
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addReview(Review $review): void
    {
        $this->reviewRepository->save($review);
        $this->flashBag->set("success", "Review added!");
    }


    /**
     * @param Review $review
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteReview(Review $review): void
    {
        $this->reviewRepository->delete($review);
        $this->flashBag->add('success','Your review was removed');
    }
}