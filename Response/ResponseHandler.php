<?php
/**
 * This file is part of the vardius/crud-bundle package
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Response;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * Class ResponseHandler
 * @package Vardius\Bundle\CrudBundle\Response
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ResponseHandler implements ResponseHandlerInterface
{
    protected static $TEMPLATE_DIR = 'VardiusCrudBundle:Actions:';
    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $templateEngine = '.html.twig';

    /**
     * @param TwigEngine $templating
     */
    function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @inheritDoc
     */
    public function getResponse($responseType, $view, $templateName, $params)
    {
        if ($responseType === 'html') {
            return new Response($this->getHtml($view, $templateName, $params));
        } elseif ($responseType === 'json') {
            return new JsonResponse($params);
        } elseif ($responseType === 'xml') {
            throw new \Exception('xml responses are not supported yet');
        }
    }

    /**
     * @inheritDoc
     */
    public function getHtml($view, $templateName, $params)
    {
        $template = null;
        if ($this->templating->exists($templateName)) {
            $template = $templateName;
        }

        if ($template === null) {
            $templateDir = $templateName.$this->templateEngine;
            if ($this->templating->exists($templateDir)) {
                $template = $templateDir;
            }
        }

        $viewPath = $view;
        if ($template === null && $viewPath) {
            $templateDir = $viewPath.$templateName.$this->templateEngine;
            if ($this->templating->exists($templateDir)) {
                $template = $templateDir;
            }
        }

        if ($template === null) {
            $templateDir = static::$TEMPLATE_DIR.$templateName.$this->templateEngine;
            if ($this->templating->exists($templateDir)) {
                $template = $templateDir;
            }
        }

        if ($template === null) {
            throw new ResourceNotFoundException(
                'ResponseHandler: View for '.$templateName.' does not exist!'
            );
        }

        return $this->templating->render($template, $params);
    }

    /**
     * @inheritDoc
     */
    public function getRefererUrl(CrudController $controller, Request $request, $params = [])
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

}
