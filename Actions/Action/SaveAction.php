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

/**
 * SaveAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class SaveAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event)
    {
        $request = $event->getRequest();
        $dataProvider = $event->getDataProvider();
        $controller = $event->getController();
        $dispatcher = $controller->get('event_dispatcher');
        $formProvider = $controller->get('vardius_crud.form.provider');

        if ($id = $request->get('id')) {
            $data = $dataProvider->get($id);
        } else {
            $data = $dataProvider->create();
        }

        $repository = $dataProvider->getSource();
        $form = $formProvider->createForm($event->getFormType(), $data);

        $crudEvent = new CrudEvent($repository, $controller, $form);
        $dispatcher->dispatch(CrudEvents::CRUD_PRE_SAVE, $crudEvent);

        $responseHandler = $this->getResponseHandler($controller);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($data->getId()) {
                    $dispatcher->dispatch(CrudEvents::CRUD_PRE_UPDATE, $crudEvent);
                    $dataProvider->update($data);
                    $dispatcher->dispatch(CrudEvents::CRUD_POST_UPDATE, $crudEvent);
                } else {
                    $dispatcher->dispatch(CrudEvents::CRUD_PRE_CREATE, $crudEvent);
                    $dataProvider->add($data);
                    $dispatcher->dispatch(CrudEvents::CRUD_POST_CREATE, $crudEvent);
                }

                $dispatcher->dispatch(CrudEvents::CRUD_POST_SAVE, $crudEvent);

                $routeName = rtrim(rtrim($request->get('_route'), 'edit'), 'add') . 'show';
                if (!$controller->get('router')->getRouteCollection()->get($routeName)) {
                    $routeName = rtrim($routeName, 'show') . 'list';
                }

                if (!$controller->get('router')->getRouteCollection()->get($routeName)) {
                    $flashBag = $request->getSession()->getFlashBag();
                    $flashBag->add('success', 'save.success');

                    return $controller->redirect($responseHandler->getRefererUrl($controller, $request, [
                        'id' => $data->getId()
                    ]));
                }

                return $controller->redirect($controller->generateUrl($routeName, [
                    'id' => $data->getId()
                ]));
            }
        }

        $params = [
            'form' => $form->createView(),
            'data' => $data,
        ];

        $crudEvent = new CrudEvent($repository, $event->getController(), $params);
        $params = $dispatcher->dispatch(CrudEvents::CRUD_SAVE_PRE_RESPONSE, $crudEvent)->getData();

        return $responseHandler->getResponse($this->options['response_type'], $event->getView(), $this->getTemplate(), $params);
    }

}
