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


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
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
    /** @var FormFactory */
    protected $formFactory;
    /** @var  EntityManager */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     * @param FormFactory $formFactory
     * @param TwigEngine $templating
     */
    function __construct(EntityManager $entityManager, FormFactory $formFactory, TwigEngine $templating)
    {
        parent::__construct($templating);
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event)
    {
        $request = $event->getRequest();
        $dataProvider = $event->getDataProvider();
        $id = $request->get('id');

        $params = [];
        if ($request->isMethod("GET")) {
            $form = $this->formFactory->createBuilder()
                ->add('submit', 'submit')
                ->getForm();

            $params['form'] = $form->createView();
        } else {
            $data = $dataProvider->get($id);

            if ($data === null) {
                throw new EntityNotFoundException('Not found error');
            }

            $crudEvent = new CrudEvent($dataProvider->getSource(), $event->getController());
            $this->dispatcher->dispatch(CrudEvents::CRUD_PRE_DELETE, $crudEvent);

            try {
                if ($data instanceof Entity) {
                    $dataProvider->remove($data);
                }
            } catch (\Exception $e) {
                $message = null;
                if (is_object($data) && method_exists($data, '__toString')) {
                    $message = 'Error while deleting "' . $data . '"';
                } else {
                    $message = 'Error while deleting element with id "' . $id . '"';
                }

                $this->getFlashBag($request)->add('error', $message);
            }

            $this->dispatcher->dispatch(CrudEvents::CRUD_POST_DELETE, $crudEvent);

            return $this->getResponse($event->getView(), $params);
        }
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
            'delete',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'delete';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDefinition()
    {
        return array(
            'pattern' => '/delete/{id}',
            'requirements' => array(
                'id' => '\d+'
            )
        );
    }

}
