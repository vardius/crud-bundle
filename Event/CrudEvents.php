<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Vardius\Bundle\CrudBundle\Event;

/**
 * CrudEvents
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
final class CrudEvents
{
    /**
     * The crud.pre.delete event is thrown each time an delete action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_PRE_DELETE = 'crud.pre.delete';

    /**
     * The crud.post.delete event is thrown each time an delete action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_POST_DELETE = 'crud.post.delete';

    /**
     * The crud.pre.save event is thrown each time an save action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_PRE_SAVE = 'crud.pre.save';

    /**
     * The crud.post.save event is thrown each time an save action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_POST_SAVE = 'crud.post.save';

    /**
     * The crud.list event is thrown each time an list action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_LIST = 'crud.list';

    /**
     * The crud.show event is thrown each time an show action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_SHOW = 'crud.show';
}
