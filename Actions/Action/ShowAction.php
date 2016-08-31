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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;
use Vardius\Bundle\CrudBundle\Event\ResponseEvent;

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
    public function call(ActionEvent $event, string $format):Response
    {
        $controller = $event->getController();
        $dataProvider = $event->getDataProvider();
        $request = $event->getRequest();

        $id = $request->get('id');
        $data = $dataProvider->get($id);
        if ($data === null) {
            throw new EntityNotFoundException('Not found error');
        }

        $this->checkRole($controller, $data);

        if ($format === 'html') {
            $params = $data;
        } else {
            if ($this->options['toArray']) {
                if (method_exists($data, 'toArray')) {
                    $data = $data->toArray();
                } else {
                    throw new MethodNotImplementedException('toArray');
                }
            }

            $params = $data;
        }

        $routeName = $request->get('_route');
        if (strpos($routeName, 'export') !== false) {
            $params['ui'] = false;
        }

        $paramsEvent = new ResponseEvent($params);
        $crudEvent = new CrudEvent($dataProvider->getSource(), $controller, $paramsEvent);

        $dispatcher = $controller->get('event_dispatcher');
        $dispatcher->dispatch(CrudEvents::CRUD_SHOW, $crudEvent);

        $responseHandler = $controller->get('vardius_crud.response.handler');

        return $responseHandler->getResponse($format, $event->getView(), $this->getTemplate(), $paramsEvent->getParams(), 200, [], ['groups' => ['show']]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('template', 'show');

        $resolver->setDefault('requirements', ['id' => '\d+']);

        $resolver->setDefault('pattern', function (Options $options) {
            return $options['rest_route'] ? '/{id}.{_format}' : '/show/{id}.{_format}';
        });

        $resolver->setDefault('defaults', function (Options $options) {
            $format = $options['rest_route'] ? 'json' : 'html';

            return [
                '_format' => $format
            ];
        });

        $resolver->setDefault('methods', function (Options $options, array $previousValue) {
            return $options['rest_route'] ? ['GET'] : $previousValue;
        });

        $resolver->setDefault('toArray', false);
        $resolver->addAllowedTypes('toArray', 'bool');
    }
}
