<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Actions\Action;


use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;

/**
 * EditAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class EditAction extends SaveAction
{
    /**
     * {@inheritdoc}
     */
    public function getEventsNames()
    {
        return array_merge(
            parent::getEventsNames(),
            [
                'edit',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDefinition()
    {
        return array(
            'pattern' => '/edit/{id}',
            'requirements' => array(
                'id' => '\d+'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchEvent(CrudEvent $crudEvent, $type)
    {
        if ($type === 'PRE') {
            $this->dispatcher->dispatch(CrudEvents::CRUD_PRE_EDIT, $crudEvent);
        } elseif ($type === 'POST') {
            $this->dispatcher->dispatch(CrudEvents::CRUD_POST_EDIT, $crudEvent);
        }
    }

}