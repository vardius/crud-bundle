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

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * Interface ResponseHandlerInterface
 * @package Vardius\Bundle\CrudBundle\Response
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ResponseHandlerInterface
{
    /**
     * @param string $responseType
     * @param string $view controller event view
     * @param string $templateName action template name
     * @param array $params
     * @return JsonResponse|Response
     */
    public function getResponse($responseType, $view, $templateName, $params);

    /**
     * @param string $view controller event view
     * @param string $templateName action template name
     * @param array $params
     * @return string
     */
    public function getHtml($view, $templateName, $params);

    /**
     * @param CrudController $controller
     * @param Request $request
     * @param array $params
     * @return mixed
     */
    public function getRefererUrl(CrudController $controller, Request $request, $params = []);

}
