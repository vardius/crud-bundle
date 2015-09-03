<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Manager;

/**
 * CrudManagerInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface CrudManagerInterface
{
    /**
     * Get entity custom logic
     *
     * @param $id
     */
    public function get($id);

    /**
     * Remove entity custom logic
     *
     * @param $entity
     */
    public function remove($entity);

    /**
     * Add entity custom logic
     * @param $entity
     */
    public function add($entity);

    /**
     * Updates entity custom logic
     *
     * @param $entity
     */
    public function update($entity);
}
