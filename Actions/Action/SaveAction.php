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


use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;
use Vardius\Bundle\CrudBundle\Form\Provider\FormProvider;

/**
 * SaveAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class SaveAction extends Action
{
    /** @var EventDispatcherInterface */
    protected $dispatcher;
    /** @var FormProvider */
    protected $formProvider;

    /**
     * @param FormProvider $formProvider
     * @param TwigEngine $templating
     */
    function __construct(FormProvider $formProvider, TwigEngine $templating)
    {
        parent::__construct($templating);
        $this->formProvider = $formProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event)
    {
        $request = $event->getRequest();
        $dataProvider = $event->getDataProvider();

        if ($id = $request->get('id')) {
            $data = $dataProvider->get($id);
        } else {
            $data = $dataProvider->create();
        }

        $form = $this->formProvider->createForm($event->getFormType(), $data);

        $crudEvent = new CrudEvent($dataProvider->getSource(), $event->getController(), $form);
        $this->dispatcher->dispatch(CrudEvents::CRUD_PRE_SAVE, $crudEvent);
        $this->dispatchEvent($crudEvent, 'PRE');

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($data->getId()) {
                    $dataProvider->update($data);
                } else {
                    $dataProvider->add($data);
                }

                $controller = $event->getController();

                return $controller->redirect($controller->generateUrl('', [
                    'id' => $data->getId()
                ]));
            }
        }

        $this->dispatcher->dispatch(CrudEvents::CRUD_POST_SAVE, $crudEvent);
        $this->dispatchEvent($crudEvent, 'POST');

        return $this->getResponse($event->getView(), [
            'form' => $form->createView(),
            'data' => $data,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventsNames()
    {
        return [
            'save',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'edit';
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @param CrudEvent $crudEvent
     * @param $type
     * @return mixed
     */
    abstract public function dispatchEvent(CrudEvent $crudEvent, $type);
}