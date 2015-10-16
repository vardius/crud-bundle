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
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Vardius\Bundle\CrudBundle\Actions\Provider\ActionsProvider;
use Vardius\Bundle\CrudBundle\Controller\CrudController;
use Vardius\Bundle\CrudBundle\Manager\CrudManagerInterface;
use Vardius\Bundle\CrudBundle\Data\Provider\Doctrine\DataProvider;
use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProviderInterface;

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

    /**
     * @param array $actions
     * @param ContainerInterface $container
     */
    function __construct(array $actions, ContainerInterface $container)
    {
        $this->actions = new ArrayCollection($actions);
        $this->container = $container;
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param $routePrefix
     * @param $entityName
     * @param ListViewProviderInterface $listViewProvider
     * @param AbstractType $formType
     * @param CrudManagerInterface $crudManager
     * @param string $view
     * @param array|ActionsProvider $actions
     *
     * @throws EntityNotFoundException
     * @return CrudController
     */
    public function get($entityName, $routePrefix = '', ListViewProviderInterface $listViewProvider = null, AbstractType $formType = null, CrudManagerInterface $crudManager = null, $view = null, $actions = [])
    {
        $repo = $this->entityManager->getRepository($entityName);

        if ($repo === null) {
            throw new EntityNotFoundException('CrudFactory: Invalid entity alias "' . $entityName . '"');
        }

        $dataProvider = new DataProvider($repo, $this->entityManager, $crudManager);
        $controller = new CrudController($dataProvider, $routePrefix, $listViewProvider, $formType, $view);
        $controller->setContainer($this->container);

        if ($actions instanceof ActionsProvider) {
            $controller->setActions($actions->getActions());
        } elseif (!empty($actions)) {
            $controller->setActions(new ArrayCollection($actions));
        } else {
            $controller->setActions($this->actions);
        }

        return $controller;
    }
}
