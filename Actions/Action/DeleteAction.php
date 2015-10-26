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
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;

/**
 * DeleteAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DeleteAction extends Action
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
        $id = $request->get('id');

        $data = $dataProvider->get($id);

        if ($data === null) {
            throw new EntityNotFoundException('Not found error');
        }

        $crudEvent = new CrudEvent($dataProvider->getSource(), $controller);
        $dispatcher->dispatch(CrudEvents::CRUD_PRE_DELETE, $crudEvent);

        try {
            $dataProvider->remove($data->getId());
        } catch (\Exception $e) {
            $message = null;
            if (is_object($data) && method_exists($data, '__toString')) {
                $message = 'Error while deleting "' . $data . '"';
            } else {
                $message = 'Error while deleting element with id "' . $id . '"';
            }

            $flashBag = $request->getSession()->getFlashBag();
            $flashBag->add('error', $message);
        }

        $dispatcher->dispatch(CrudEvents::CRUD_POST_DELETE, $crudEvent);

        return $controller->redirect($this->getResponseHandler($controller)->getRefererUrl($controller, $request));
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('requirements', [
            'id' => '\d+'
        ]);

        $resolver->setDefault('pattern', function (Options $options) {
            if ($options['rest_route']) {
                return '/{id}';
            }

            return '/delete/{id}';
        });

        $resolver->setDefault('methods', function (Options $options, $previousValue) {
            if ($options['rest_route']) {
                return ['DELETE'];
            }

            return $previousValue;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'delete';
    }

}
