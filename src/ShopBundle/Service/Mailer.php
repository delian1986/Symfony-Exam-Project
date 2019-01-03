<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\User;

class Mailer implements MailerInterface
{
    CONST FROM = 'shop@delyan.eu';
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $templating;

    public function __construct(\Swift_Mailer $mailer,
                                \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendRegistration(User $recipient)
    {
        $body = $this->templating->render('email/register.html.twig', ['name' => $recipient->getFullName()]);
        $subject = "Hello {$recipient->getFullName()}";

        $message = $this->messageBuilder($subject, $recipient->getEmail(), $body);
        $this->send($message);
    }

    public function sendCartCheckOut(Order $order)
    {
        $body = $this->templating->render('email/cart_checkout.html.twig', ['name' => $order->getUser()->getFullName(), 'order' => $order]);
        $subject = "{$order->getUser()->getFullName()}, your order is {$order->getStatus()->getName()}!";

        $message = $this->messageBuilder($subject, $order->getUser()->getEmail(), $body);
        $this->send($message);
    }

    public function sendDeclinedOrderNotify(Order $order, $reason)
    {
        $body = $this->templating->render('email/cart_checkout.html.twig', ['name' => $order->getUser()->getFullName(), 'order' => $order, 'reason' => $reason]);
        $subject = "{$order->getUser()->getFullName()}, your order was declined!";

        $message = $this->messageBuilder($subject, $order->getUser()->getEmail(), $body);
        $this->send($message);
    }


    private function messageBuilder(string $subject, string $to, $body)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(self::FROM)
            ->setTo($to)
            ->setBody(
                $body, 'text/html'
            );

        return $message;
    }

    private function send($message)
    {
        $this->mailer->send($message);
    }
}