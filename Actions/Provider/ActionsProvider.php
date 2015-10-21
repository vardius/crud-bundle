<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Actions\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Vardius\Bundle\CrudBundle\Actions\Factory\ActionFactory;

/**
 * Class ActionsProvider
 * @package Vardius\Bundle\CrudBundle\Actions\Provider
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class ActionsProvider implements ActionsProviderInterface
{
    /** @var ArrayCollection */
    protected $actions;
    /** @var  ActionFactory */
    protected $actionFactory;

    /**
     * ActionsProvider constructor.
     * @param ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
        $this->actions = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param string $type
     * @param array $options
     * @return $this
     */
    protected function addAction($type, array $options=[])
    {
        $action = $this->actionFactory->get($type, $options);
        $suffix = $action->getOptions()['route_suffix'];
        $key = (empty($suffix) ? $action->getName() : $suffix);
        $this->actions->set($key, $action);

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    protected function removeAction($key)
    {
        $this->actions->remove($key);

        return $this;
    }

}
