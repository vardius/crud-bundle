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


use Doctrine\ORM\EntityNotFoundException;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;

/**
 * ShowAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ShowAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event)
    {
        $request = $event->getRequest();
        $dataProvider = $event->getDataProvider();
        $id = $request->get('id');

        $data = $dataProvider->get($id);

        if ($data === null) {
            throw new EntityNotFoundException('Not found error');
        }
        $crudEvent = new CrudEvent($dataProvider->getSource(), $event->getController());
        $this->dispatcher->dispatch(CrudEvents::CRUD_SHOW, $crudEvent);

        return $this->getResponse($event->getView(), [
            'data' => $data,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventsNames()
    {
        return [
            'show',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDefinition()
    {
        return array(
            'pattern' => '/show/{id}',
            'requirements' => array(
                'id' => '\d+'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'show';
    }

}
