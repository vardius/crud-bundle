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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vardius\Bundle\CrudBundle\Actions\ActionInterface;
use Vardius\Bundle\CrudBundle\Data\DataProviderInterface;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;

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

    /**
     * @param DataProviderInterface $dataProvider
     * @param string $routePrefix
     * @param AbstractType $formType
     * @param string $view
     */
    function __construct(DataProviderInterface $dataProvider, $routePrefix = '', AbstractType $formType = null, $view = null)
    {
        $this->dataProvider = $dataProvider;
        $this->routePrefix = $routePrefix;
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

        return $action->call($event, $request->getRequestFormat());
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

    /**
     * Throws an exception unless the attributes are granted against the current authentication token and optionally
     * supplied object.
     *
     * @param mixed $attributes The attributes
     * @param mixed $object The object
     * @param string $message The message passed to the exception
     *
     * @throws AccessDeniedException
     */
    public function checkAccess($attributes, $object = null, $message = 'Access Denied.')
    {
        $this->denyAccessUnlessGranted($attributes, $object, $message);
    }

    /**
     * Gets a container service by its id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route The name of the route
     * @param mixed $parameters An array of parameters
     * @param int $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url The URL to redirect to
     * @param int $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

}
