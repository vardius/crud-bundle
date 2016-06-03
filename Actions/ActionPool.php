<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Actions;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ActionPool
 * @package Vardius\Bundle\CrudBundle\Actions
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ActionPool
{
    /** @var ArrayCollection */
    protected $actions;

    /**
     * {@inheritdoc}
     */
    function __construct()
    {
        $this->actions = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getActions():ArrayCollection
    {
        return $this->actions;
    }

    /**
     * @param ActionInterface $action
     * @return ActionPool
     */
    public function addAction(ActionInterface $action):self
    {
        $this->actions->set($action->getName(), $action);
        return $this;
    }

    /**
     * @param string $id
     * @return ActionInterface
     */
    public function getAction(string $id):ActionInterface
    {
        return $this->actions->get($id);
    }
}
