<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\User;
use ShopBundle\Repository\ShopOwnerRepository;

class Mailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $templating;

    /**
     * @var ShopOwnerRepository
     */
    private $shopOwnerRepository;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $templating
     * @param ShopOwnerRepository $shopOwnerRepository
     */
    public function __construct(\Swift_Mailer $mailer,
                                \Twig_Environment $templating,
                                ShopOwnerRepository $shopOwnerRepository)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->shopOwnerRepository=$shopOwnerRepository;
    }

    /**
     * @param User $recipient
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendRegistration(User $recipient)
    {
        $body = $this->templating->render('email/register.html.twig', ['name' => $recipient->getFullName()]);
        $subject = "Hello {$recipient->getFullName()}";

        $message = $this->messageBuilder($subject, $recipient->getEmail(), $body);
        $this->send($message);
    }

    /**
     * @param Order $order
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCartCheckOut(Order $order)
    {
        $body = $this->templating->render('email/cart_checkout.html.twig', ['name' => $order->getUser()->getFullName(), 'order' => $order]);
        $subject = "{$order->getUser()->getFullName()}, your order is {$order->getStatus()->getName()}!";

        $message = $this->messageBuilder($subject, $order->getUser()->getEmail(), $body);
        $this->send($message);
    }

    /**
     * @param Order $order
     * @param string $reason
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendDeclinedOrderNotify(Order $order, $reason)
    {
        $body = $this->templating->render('email/cart_checkout.html.twig', ['name' => $order->getUser()->getFullName(), 'order' => $order, 'reason' => $reason]);
        $subject = "{$order->getUser()->getFullName()}, your order was declined!";

        $message = $this->messageBuilder($subject, $order->getUser()->getEmail(), $body);
        $this->send($message);
    }

    private function messageBuilder(string $subject, string $to, $body)
    {
        $shopOwner =$this->shopOwnerRepository->getShopOwner();
        $from=$shopOwner->getShopOwner()->getEmail();

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $body, 'text/html'
            );

        return $message;
    }

    /**
     * @param User $oldOwner
     * @param User $newOwner
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendShopOwnerNotice(User $oldOwner, User $newOwner)
    {
        $body = $this->templating->render('email/shop_owner_notice.html.twig', ['oldOwner' => $oldOwner, 'newOwner' => $newOwner]);
        $subject = "{$newOwner->getFullName()}, you are now shop owner!";
        $message = $this->messageBuilder($subject, $newOwner->getEmail(), $body);

        $this->send($message);

    }


    private function send($message)
    {
        $this->mailer->send($message);
    }
}