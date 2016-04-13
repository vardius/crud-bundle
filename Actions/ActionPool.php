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
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions->set($action->getName(), $action);
    }

    /**
     * @param string $id
     * @return Action
     */
    public function getAction($id)
    {
        return $this->actions->get($id);
    }

}