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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * Interface ResponseHandlerInterface
 * @package Vardius\Bundle\CrudBundle\Response
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ResponseHandlerInterface
{
    /**
     * @param string $format
     * @param string $view controller event view
     * @param string $templateName action template name
     * @param array $params
     * @param int $status The response status code
     * @param array $headers An array of response headers
     * @param array $context An context array for serialization
     * @return Response
     */
    public function getResponse(string $format, string $view, string $templateName, array $params, int $status = 200, array $headers = [], array $context = []):Response;

    /**
     * @param CrudController $controller
     * @param Request $request
     * @param array $params
     * @return string
     */
    public function getRefererUrl(CrudController $controller, Request $request, array $params = []):string;
}
