<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Actions\Factory;

use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Actions\ActionInterface;
use Vardius\Bundle\CrudBundle\Actions\ActionPool;

/**
 * Class ActionFactory
 * @package Vardius\Bundle\CrudBundle\Actions\Factory
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ActionFactory
{
    /** @var  ActionPool */
    protected $actionPool;

    /**
     * @param ActionPool $actionPool
     */
    function __construct(ActionPool $actionPool)
    {
        $this->actionPool = $actionPool;
    }

    /**
     * @param mixed $action
     * @param array $options
     * @return ActionInterface
     */
    public function get($action, array $options = []):ActionInterface
    {
        if (is_string($action)) {
            $action = $this->actionPool->getAction($action);
        }

        if (!$action instanceof ActionInterface) {
            throw new \InvalidArgumentException('The $action mast be instance of ActionInterface. ' . $action . ' given');
        }

        $action = clone $action;
        $action->setOptions($options);

        return $action;
    }
}
