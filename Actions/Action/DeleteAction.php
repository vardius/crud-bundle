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
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
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
    public function call(ActionEvent $event, $format)
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

        $crudEvent = new CrudEvent($dataProvider->getSource(), $controller);
        $dispatcher = $controller->get('event_dispatcher');
        $dispatcher->dispatch(CrudEvents::CRUD_PRE_DELETE, $crudEvent);

        try {
            $dataProvider->remove($data->getId());

            $response = [
                'success' => true,
            ];
        } catch (\Exception $e) {
            $message = null;
            if (is_object($data) && method_exists($data, '__toString')) {
                $message = 'Error while deleting "' . $data . '"';
            } else {
                $message = 'Error while deleting element with id "' . $id . '"';
            }

            $response = [
                'success' => false,
                'error' => $message,
            ];

            /** @var Session $session */
            $session = $request->getSession();
            /** @var FlashBagInterface $flashBag */
            $flashBag = $session->getFlashBag();
            $flashBag->add('error', $message);
        }

        $dispatcher->dispatch(CrudEvents::CRUD_POST_DELETE, $crudEvent);
        $responseHandler = $controller->get('vardius_crud.response.handler');

        if ($format === 'html') {

            return $controller->redirect($responseHandler->getRefererUrl($controller, $request));
        } else {

            return $responseHandler->getResponse($format, '', '', $response);
        }
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
                return '/{id}.{_format}';
            }

            return '/delete/{id}.{_format}';
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
