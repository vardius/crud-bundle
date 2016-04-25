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

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;
use Vardius\Bundle\CrudBundle\Event\ResponseEvent;

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
    public function call(ActionEvent $event, $format)
    {
        $controller = $event->getController();

        $this->checkRole($controller);

        $dataProvider = $event->getDataProvider();
        $params = [
            'data' => $dataProvider->findAll(),
        ];

        $request = $event->getRequest();
        $routeName = $request->get('_route');
        if (strpos($routeName, 'export') !== false) {
            $params['ui'] = false;
        }

        $source = $dataProvider->getSource();
        $paramsEvent = new ResponseEvent($params);
        $crudEvent = new CrudEvent($source, $controller, $paramsEvent);

        $dispatcher = $controller->get('event_dispatcher');
        $dispatcher->dispatch(CrudEvents::CRUD_LIST, $crudEvent);

        $responseHandler = $controller->get('vardius_crud.response.handler');

        return $responseHandler->getResponse($format, $event->getView(), $this->getTemplate(), $paramsEvent->getParams(), 200, [], ['list']);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('pattern', function (Options $options) {
            return $options['rest_route'] ? '.{_format}' : '/list.{_format}';
        });

        $resolver->setDefault('defaults', function (Options $options) {
            $format = $options['rest_route'] ? 'json' : 'html';

            return [
                '_format' => $format
            ];
        });

        $resolver->setDefault('methods', function (Options $options, $previousValue) {
            return $options['rest_route'] ? ['GET'] : $previousValue;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'list';
    }

}
