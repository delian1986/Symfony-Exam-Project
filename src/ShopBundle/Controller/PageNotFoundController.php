<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class PageNotFoundController extends Controller
{
    public function pageNotFoundAction()
    {
        return $this->render('exception/error404.html.twig');
    }
}
