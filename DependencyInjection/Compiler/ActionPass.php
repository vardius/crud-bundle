<?php
/**
 * This file is part of the vardius/crud-bundle package
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ActionPass
 * @package Vardius\Bundle\CrudBundle\DependencyInjection\Compiler
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ActionPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('vardius_crud.action_pool')) {
            return;
        }

        $definition = $container->getDefinition(
            'vardius_crud.action_pool'
        );

        $actions = $container->findTaggedServiceIds(
            'vardius_crud.action'
        );
        foreach ($actions as $id => $action) {
            $definition->addMethodCall(
                'addAction',
                [new Reference($id)]
            );
        }
    }
}
