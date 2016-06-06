<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Tests\Actions\Factory;

use Vardius\Bundle\CrudBundle\Actions\Action\ShowAction;
use Vardius\Bundle\CrudBundle\Actions\ActionPool;
use Vardius\Bundle\CrudBundle\Actions\Factory\ActionFactory;

/**
 * Class ActionFactoryTest
 * @package Vardius\Bundle\CrudBundle\Tests\Actions\Factory
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInvalidAction()
    {
        $factory = new ActionFactory(new ActionPool());

        $this->setExpectedException('InvalidArgumentException');

        $factory->get(null);
    }

    public function testGetStringAction()
    {
        $action = new ShowAction();
        $pool = new ActionPool();
        $pool->addAction($action);

        $factory = new ActionFactory($pool);

        $this->assertInstanceOf('Vardius\Bundle\CrudBundle\Actions\Action\ShowAction', $factory->get(get_class($action)));
    }

    public function testGetObjectAction()
    {
        $factory = new ActionFactory(new ActionPool());

        $this->assertInstanceOf('Vardius\Bundle\CrudBundle\Actions\Action', $factory->get(ShowAction::class));
    }
}
