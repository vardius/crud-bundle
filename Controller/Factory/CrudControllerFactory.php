<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Controller\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Vardius\Bundle\CrudBundle\Actions\Provider\ActionsProviderInterface;
use Vardius\Bundle\CrudBundle\Controller\CrudController;
use Vardius\Bundle\CrudBundle\Manager\CrudManagerInterface;
use Vardius\Bundle\CrudBundle\Data\Provider;
use Vardius\Bundle\SecurityBundle\Security\Authorization\Voter\SupportedClassPool;

/**
 * CrudControllerFactory
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudControllerFactory
{
    /** @var  ArrayCollection */
    protected $actions;
    /** @var  EntityManager */
    protected $entityManager;
    /** @var  ContainerInterface */
    protected $container;
    /** @var  SupportedClassPool */
    protected $securityClassPool;

    /**
     * @param array $actions
     * @param ContainerInterface $container
     */
    function __construct(array $actions, ContainerInterface $container)
    {
        $this->actions = new ArrayCollection($actions);
        $this->container = $container;

        if ($this->container->has('doctrine.orm.entity_manager')) {
            $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        }

        if ($this->container->has('vardius_security.voter.supported_class_pool')) {
            $this->securityClassPool = $this->container->get('vardius_security.voter.supported_class_pool');
        }
    }

    /**
     * @param $routePrefix
     * @param $entityName
     * @param AbstractType $formType
     * @param CrudManagerInterface $crudManager
     * @param string $view
     * @param array|ActionsProvider $actions
     * @param string $controller
     *
     * @throws \Exception
     * @return CrudController
     */
    public function get($entityName, $routePrefix = '', AbstractType $formType = null, CrudManagerInterface $crudManager = null, $view = null, $actions = [], $controller = 'Vardius\Bundle\CrudBundle\Controller\CrudController')
    {
        switch ($this->container->getParameter('vardius_crud.db_driver')) {
            case 'propel':
                if (!class_exists($entityName)) {
                    throw new \Exception('CrudFactory: Invalid entity alias "' . $entityName . '"');
                }

                $this->registerSupportedClass($entityName);

                $dataProvider = new Provider\Propel\DataProvider($entityName, $crudManager);
                break;
            default:
                $repo = $this->entityManager->getRepository($entityName);

                if ($repo === null) {
                    throw new \Exception('CrudFactory: Invalid entity alias "' . $entityName . '"');
                }

                $this->registerSupportedClass($repo->getClassName());

                $dataProvider = new Provider\Doctrine\DataProvider($repo, $this->entityManager, $crudManager);
                break;
        }

        $controller = new $controller($dataProvider, $routePrefix, $formType, $view);
        if (!$controller instanceof CrudController) {
            throw new \Exception('CrudFactory: Invalid controller class "' . get_class($controller) . '"');
        }

        $controller->setContainer($this->container);

        if ($actions instanceof ActionsProviderInterface) {
            $controller->setActions($actions->getActions());
        } elseif (!empty($actions)) {
            $controller->setActions(new ArrayCollection($actions));
        } else {
            $controller->setActions($this->actions);
        }

        return $controller;
    }

    protected function registerSupportedClass($class)
    {
        if ($this->securityClassPool) {
            $this->securityClassPool->addClass($class);
        }
    }
}
