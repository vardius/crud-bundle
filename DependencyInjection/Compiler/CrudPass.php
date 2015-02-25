<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * CrudPass
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudPass implements CompilerPassInterface {
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('vardius_crud.routing.pool');

        foreach($container->findTaggedServiceIds('vardius_crud.controller') as $id => $attributes){
            $definition->addMethodCall('addController', array($id, new Reference($id)));
        }
    }

}
