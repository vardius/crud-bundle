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


/**
 * AddAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class AddAction extends SaveAction
{
    /**
     * {@inheritdoc}
     */
    public function getEventsNames()
    {
        return array_merge(
            [
                'add',
            ],
            parent::getEventsNames()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDefinition()
    {
        return array('pattern' => '/add');
    }

}
