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
     * @return string
     */
    public function getEventsNames();

    /**
     * Returns action's template name
     *
     * @return string
     */
    public function getTemplateName();

    /**
     * Returns route definitions for action
     *
     * Available array options:
     * return array(
     *      'pattern' => '',
     *      'defaults' => array(),
     *      'requirements' => array(),
     *      'options' => array(),
     *      'host' => '',
     *      'schemes' => array(),
     *      'methods' => array(),
     *      'condition' => ''
     * );
     *
     * string           pattern         The path pattern to match
     * array            defaults        An array of default parameter values
     * array            requirements    An array of requirements for parameters (regexes)
     * array            options         An array of options
     * string           host            The host pattern to match
     * string|array     schemes         A required URI scheme or an array of restricted schemes
     * string|array     methods         A required HTTP method or an array of restricted methods
     * string           condition       A condition that should evaluate to true for the route to match
     *
     * @return array
     */
    public function getRouteDefinition();
}
