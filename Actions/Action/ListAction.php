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


use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;

/**
 * ListAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event)
    {
        $repository = $event->getDataProvider()->getSource();

        $crudEvent = new CrudEvent($repository, $event->getController());
        $this->dispatcher->dispatch(CrudEvents::CRUD_LIST, $crudEvent);

        $listView = $event->getListView();

        $params = [];
        if ($listView !== null) {
            $data = $listView->getData(new ListDataEvent($repository, $event->getRequest()));
            $params = [
                'data' => $data['results'],
                'filterForms' => $data['filterForms'],
                'paginator' => $data['paginator'],
                'columns' => $listView->getColumns(),
                'actions' => $listView->getActions(),
                'title' => $listView->getTitle(),
            ];
        }

        return $this->getResponse($event->getView(), $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventsNames()
    {
        return [
            'list',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDefinition()
    {
        return array(
            'pattern' => '/list/{page}/{limit}/{column}/{sort}',
            'defaults' => array(
                'page' => 1,
                'limit' => 1,
                'column' => null,
                'sort' => 'asc'
            ),
            'requirements' => array(
                'page' => '\d+',
                'limit' => '\d+',
                'sort' => 'asc|desc'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'list';
    }

}
