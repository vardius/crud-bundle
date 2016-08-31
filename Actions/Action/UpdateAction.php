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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;

/**
 * UpdateAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class UpdateAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event, string $format):Response
    {
        $controller = $event->getController();
        $dataProvider = $event->getDataProvider();
        $request = $event->getRequest();

        $data = $dataProvider->get($request->get('id'));

        $this->checkRole($controller, $data);

        $allowed = $this->getOptions()['allow'];
        $properties = $request->get('data', []);

        $accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();

        foreach ($properties as $property => $value) {
            if (in_array($property, $allowed) && $accessor->isWritable($data, $property)) {
                $accessor->setValue($data, $property, $value);
            }
        }

        $validator = $controller->get('validator');
        $errors = $validator->validate($data, null, ["update"]);

        $responseHandler = $controller->get('vardius_crud.response.handler');

        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => 'Invalid data',
                'errors' => $errors,
            ], 400);
        } else {
            $source = $dataProvider->getSource();
            $crudEvent = new CrudEvent($source, $controller);

            $dispatcher = $controller->get('event_dispatcher');

            $dispatcher->dispatch(CrudEvents::CRUD_PRE_UPDATE, $crudEvent);
            $dataProvider->update($data);
            $dispatcher->dispatch(CrudEvents::CRUD_POST_UPDATE, $crudEvent);

            return $responseHandler->getResponse($format, '', '', $data, 200, [], ['groups' => ['update']]);
        }
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->remove('template');

        $resolver->setDefault('allow', []);
        $resolver->setAllowedTypes('allow', 'array');

        $resolver->setDefault('requirements', ['id' => '\d+']);

        $resolver->setDefault('pattern', function (Options $options) {
            return $options['rest_route'] ? '/{id}.{_format}' : '/update/{id}.{_format}';
        });

        $resolver->setDefault('defaults', function (Options $options) {
            $format = $options['rest_route'] ? 'json' : 'html';

            return [
                '_format' => $format
            ];
        });

        $resolver->setDefault('methods', function (Options $options, array $previousValue) {
            return $options['rest_route'] ? ['PATCH'] : $previousValue;
        });

        $resolver->setDefault('parameters', [
            ['name' => 'data', 'dataType' => 'json', "required" => true, 'description' => 'Array of properties to change'],
        ]);
    }
}
