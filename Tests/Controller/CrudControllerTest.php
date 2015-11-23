<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * Class CrudControllerTest
 * @package Vardius\Bundle\CrudBundle\Tests\Controller
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testCallInvalidAction()
    {
        $provider = $this
            ->getMockBuilder('Vardius\Bundle\CrudBundle\Data\DataProviderInterface')
            ->getMock();

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $controller = new CrudController($provider);
        $controller->callAction(null, new Request());
    }
}
