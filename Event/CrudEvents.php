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
     * The vardius_crud.pre.delete event is thrown each time an delete action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_PRE_DELETE = 'vardius_crud.pre.delete';

    /**
     * The vardius_crud.post.delete event is thrown each time an delete action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_POST_DELETE = 'vardius_crud.post.delete';

    /**
     * The vardius_crud.pre.save event is thrown each time an save action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_PRE_SAVE = 'vardius_crud.pre.save';

    /**
     * The vardius_crud.post.save event is thrown each time an save action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_POST_SAVE = 'vardius_crud.post.save';

    /**
     * The vardius_crud.pre.update event is thrown each time an update action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_PRE_UPDATE = 'vardius_crud.pre.update';

    /**
     * The vardius_crud.post.update event is thrown each time an update action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_POST_UPDATE = 'vardius_crud.post.update';

    /**
     * The vardius_crud.pre.create event is thrown each time an create action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_PRE_CREATE = 'vardius_crud.pre.create';

    /**
     * The vardius_crud.post.create event is thrown each time an create action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_POST_CREATE = 'vardius_crud.post.create';

    /**
     * The vardius_crud.list event is thrown each time an list action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_LIST = 'vardius_crud.list';

    /**
     * The vardius_crud.show event is thrown each time an show action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_SHOW = 'vardius_crud.show';

    /**
     * The vardius_crud.export event is thrown each time an export action is called
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_EXPORT = 'vardius_crud.export';

    /**
     * The vardius_crud.list.pre_response event is thrown each time before list action response return
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_LIST_PRE_RESPONSE = 'vardius_crud.list.pre_response';

    /**
     * The vardius_crud.save.pre_response event is thrown each time before add/edit action response return
     *
     * The event listener receives an
     * Vardius\Bundle\CrudBundle\Event\CrudEvent instance.
     *
     * @var string
     */
    const CRUD_SAVE_PRE_RESPONSE = 'vardius_crud.save.pre_response';
}
