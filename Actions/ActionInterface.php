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

use Vardius\Bundle\CrudBundle\Event\ActionEvent;

/**
 * ActionInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ActionInterface
{
    /**
     * Action body, method is invoked when the action is called from controller
     *
     * @param ActionEvent $event
     * @return mixed
     */
    public function call(ActionEvent $event);

    /**
     * Returns name of action's events
     *
     * @return mixed
     */
    public function getEventsNames();

    /**
     * Returns route definitions for action
     *
     * @return mixed
     */
    public function getRouteDefinition();

    /**
     * Returns action's template name
     *
     * @return mixed
     */
    public function getTemplateName();
}
