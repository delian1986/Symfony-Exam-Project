<?php


namespace ShopBundle\Security;

use ShopBundle\ShopBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * @var Environment
     */
    private $templating;

    public function __construct(Environment $templating)
    {
        $this->templating=$templating;
    }

    /**
     * @param Request $request
     * @param AccessDeniedException $accessDeniedException
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $content=$this->templating->render('exception/error403.html.twig');
        return new Response($content, 403);
    }
}
