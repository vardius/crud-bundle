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
    public function call(ActionEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        $dataProvider = $event->getDataProvider();

        $format = $request->getRequestFormat();
        $dispatcher = $controller->get('event_dispatcher');
        $id = $request->get('id');

        $data = $dataProvider->get($id);
        if ($data === null) {
            throw new EntityNotFoundException('Not found error');
        }

        $this->checkRole($controller, $data);

        if ($format === 'html') {
            $params = [
                'data' => $data,
            ];
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

        $paramsEvent = new ResponseEvent($params);
        $crudEvent = new CrudEvent($dataProvider->getSource(), $controller, $paramsEvent);
        $dispatcher->dispatch(CrudEvents::CRUD_SHOW, $crudEvent);

        return $this->getResponseHandler($controller)->getResponse($format, $event->getView(), $this->getTemplate(), $paramsEvent->getParams(), 200, [], ['Default', 'show']);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('requirements', ['id' => '\d+']);

        $resolver->setDefault('pattern', function (Options $options) {
            if ($options['rest_route']) {
                return '/{id}.{_format}';
            }

            return '/show/{id}.{_format}';
        });

        $resolver->setDefault('methods', function (Options $options, $previousValue) {
            if ($options['rest_route']) {
                return ['GET'];
            }

            return $previousValue;
        });

        $resolver->setDefault('toArray', false);
        $resolver->addAllowedTypes(
            [
                'toArray' => 'bool',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'show';
    }

}
