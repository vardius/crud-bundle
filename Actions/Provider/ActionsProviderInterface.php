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

/**
 * Interface ActionsProviderInterface
 * @package Vardius\Bundle\CrudBundle\Actions\Provider
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ActionsProviderInterface
{
    /**
     * Provides actions for controller
     *
     * @return ArrayCollection
     */
    public function getActions();
}
