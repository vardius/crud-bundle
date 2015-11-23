<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Tests\Controller\Factory;

use Vardius\Bundle\CrudBundle\Controller\Factory\CrudControllerFactory;

/**
 * Class CrudControllerFactoryTest
 * @package Vardius\Bundle\CrudBundle\Tests\Controller\Factory
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInvalidEntityName()
    {
        $container = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->getMock();

        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException('Doctrine\ORM\EntityNotFoundException');

        $factory = new CrudControllerFactory([], $container);
        $reflection = new \ReflectionClass($factory);
        $property = $reflection->getProperty('entityManager');
        $property->setAccessible(true);

        $property->setValue($factory, $entityManager);

        $factory->get(null);
    }
}
