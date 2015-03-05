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
use Symfony\Component\HttpFoundation\Request;
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
        $controller = $event->getController();

        if ($id = $request->get('id')) {
            $data = $dataProvider->get($id);
        } else {
            $data = $dataProvider->create();
        }

        $form = $this->formProvider->createForm($event->getFormType(), $data);

        $crudEvent = new CrudEvent($dataProvider->getSource(), $controller, $form);
        $this->dispatcher->dispatch(CrudEvents::CRUD_PRE_SAVE, $crudEvent);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($data->getId()) {
                    $this->dispatcher->dispatch(CrudEvents::CRUD_PRE_UPDATE, $crudEvent);
                    $dataProvider->update($data);
                    $this->dispatcher->dispatch(CrudEvents::CRUD_POST_UPDATE, $crudEvent);
                } else {
                    $this->dispatcher->dispatch(CrudEvents::CRUD_PRE_CREATE, $crudEvent);
                    $dataProvider->add($data);
                    $this->dispatcher->dispatch(CrudEvents::CRUD_POST_CREATE, $crudEvent);
                }

                $this->dispatcher->dispatch(CrudEvents::CRUD_POST_SAVE, $crudEvent);

                $routeName = rtrim(rtrim($request->get('_route'), 'edit'), 'add') . 'show';
                if (!$controller->get('router')->getRouteCollection()->get($routeName)) {
                    $routeName = rtrim($routeName, 'show') . 'list';
                }

                if (!$controller->get('router')->getRouteCollection()->get($routeName)) {
                    $this->getFlashBag($request)->add('success', 'save.success');

                    return $controller->redirect($this->getRefererUrl($controller, $request, [
                        'id' => $data->getId()
                    ]));
                }

                return $controller->redirect($controller->generateUrl($routeName, [
                    'id' => $data->getId()
                ]));
            }
        }

        return $this->getResponse($event->getView(), [
            'form' => $form->createView(),
            'data' => $data,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFlashBag(Request $request)
    {
        return $request->getSession()->getFlashBag();
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

}
