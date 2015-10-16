<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vardius\Bundle\CrudBundle\Actions\ActionInterface;
use Vardius\Bundle\CrudBundle\Data\DataProviderInterface;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\ListBundle\ListView\ListView;
use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProviderInterface;

/**
 * CrudController
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudController extends Controller
{
    /** @var  DataProviderInterface */
    protected $dataProvider;
    /** @var string */
    protected $routePrefix;
    /** @var string */
    protected $view;
    /** @var AbstractType */
    protected $formType;
    /** @var ArrayCollection */
    protected $actions;
    /** @var ListView */
    protected $listView;

    /**
     * @param DataProviderInterface $dataProvider
     * @param string $routePrefix
     * @param ListViewProviderInterface $listViewProvider
     * @param AbstractType $formType
     * @param string $view
     */
    function __construct(DataProviderInterface $dataProvider, $routePrefix = '', ListViewProviderInterface $listViewProvider = null, AbstractType $formType = null, $view = null)
    {
        $this->dataProvider = $dataProvider;
        $this->routePrefix = $routePrefix;
        $this->listView = $listViewProvider ? $listViewProvider->buildListView() : $listViewProvider;
        $this->formType = $formType;
        $this->view = $view;
        $this->actions = new ArrayCollection();
    }

    /**
     * @param $_action
     * @param Request $request
     * @return mixed
     */
    public function callAction($_action, Request $request)
    {
        $event = new ActionEvent($this, $request);
        $action = $this->getAction($_action);
        if ($action === null) {
            throw new NotFoundHttpException('Action "' . $_action . '" does not exist');
        }

        return $action->call($event);
    }

    /**
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param ArrayCollection $actions
     */
    public function setActions(ArrayCollection $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param $key
     * @param ActionInterface $action
     */
    public function addAction($key, ActionInterface $action)
    {
        $this->actions->set($key, $action);
    }

    /**
     * @param $key
     */
    public function removeAction($key)
    {
        $this->actions->remove($key);
    }

    /**
     * @param $key
     *
     * @return ActionInterface
     */
    public function getAction($key)
    {
        return $this->actions->get($key);
    }

    /**
     * @return DataProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return AbstractType
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return ListView
     */
    public function getListView()
    {
        return $this->listView;
    }

    /**
     * Returns array from entity object
     * Used in export action
     *
     * @param $entity
     * @return array
     */
    public function getRow($entity)
    {
        return method_exists($entity, 'toArray') ? $entity->toArray() : [];
    }

    /**
     * Returns headers for export action (CSV file case)
     *
     * @return array
     */
    public function getHeaders()
    {
        return [];
    }

    public function redirectToPath($routeName, array $params)
    {
        return $this->redirect($this->generateUrl($routeName, $params));
    }

}
