<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Actions;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * Action
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class Action implements ActionInterface
{
    protected static $TEMPLATE_DIR = 'VardiusCrudBundle:Actions:';

    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $templateEngine = '.html.twig';
    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /**
     * @param TwigEngine $templating
     */
    function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param $view
     * @param $params
     * @return Response
     */
    protected function getResponse($view, $params)
    {
        $template = null;
        if ($this->templating->exists($this->getTemplateName())) {
            $template = $this->getTemplateName();
        }

        $viewPath = $view;
        if ($template === null && $viewPath) {
            $templateDir = $viewPath . $this->getTemplateName() . $this->templateEngine;
            if ($this->templating->exists($templateDir)) {
                $template = $templateDir;
            }
        }

        if ($template === null) {
            $templateDir = static::$TEMPLATE_DIR . $this->getTemplateName() . $this->templateEngine;
            if ($this->templating->exists($templateDir)) {
                $template = $templateDir;
            }
        }

        if ($template === null) {
            throw new ResourceNotFoundException('ResponseHandler: Wrong template path');
        }

        return new Response($this->templating->render($template, $params));
    }

    /**
     * @param CrudController $controller
     * @param Request $request
     * @param array $params
     * @return mixed
     */
    protected function getRefererUrl(CrudController $controller, Request $request, $params = [])
    {
        $referer = $request->headers->get('referer');
        $baseUrl = $request->getBaseUrl();

        $lastPath = substr($referer, strpos($referer, $baseUrl));
        $lastPath = str_replace($baseUrl, '', $lastPath);

        $matcher = $controller->get('router')->getMatcher();
        $parameters = $matcher->match($lastPath);
        $route = $parameters['_route'];

        return $controller->generateUrl($route, $params);
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }
}
