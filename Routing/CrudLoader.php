<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Vardius\Bundle\CrudBundle\Actions\ActionInterface;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * CrudLoader
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudLoader implements LoaderInterface
{
    /** @var CrudPool */
    protected $pool;

    /**
     * @param CrudPool $pool
     */
    function __construct(CrudPool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        foreach ($this->pool->getControllers() as $controllerKey => $controller) {
            /** @var CrudController $controller */
            foreach ($controller->getActions() as $actionKey => $action) {
                /** @var ActionInterface $action */
                $options = $action->getOptions();

                $pattern = $controller->getRoutePrefix() . $options['pattern'];

                $defaults = $options['defaults'];
                $defaults['_controller'] = $controllerKey . ':' . 'callAction';
                $defaults['_action'] = $actionKey;

                $route = new Route(
                    $pattern,
                    $defaults,
                    $options['requirements'],
                    $options['options'],
                    $options['host'],
                    $options['schemes'],
                    $options['methods'],
                    $options['condition']
                );

                $routeSuffix = (empty($options['route_suffix']) ? $actionKey : $options['route_suffix']);
                $routes->add($controllerKey . '.' . $routeSuffix, $route);
            }
        }

        return $routes;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return $type == 'vardius_crud';
    }

    /**
     * Gets the loader resolver.
     *
     * @return LoaderResolverInterface A LoaderResolverInterface instance
     */
    public function getResolver()
    {
    }

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolverInterface $resolver A LoaderResolverInterface instance
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

}
