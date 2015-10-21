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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
     * @return Response
     */
    public function call(ActionEvent $event);

    /**
     * Adjust the configuration of the options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);
    
    /**
     * Clear options array
     */
    public static function clearOptionsConfig();

    /**
     * Returns configuration array
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set the configuration array
     *
     * @param array $options
     */
    public function setOptions(array $options = []);

    /**
     * Returns action name
     *
     * @return string
     */
    public function getName();

}
