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
use Symfony\Component\Form\AbstractType;
use Vardius\Bundle\CrudBundle\Controller\CrudController;
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

    /**
     * @param array $actions
     * @param EntityManager $entityManager
     */
    function __construct(array $actions, EntityManager $entityManager)
    {
        $this->actions = new ArrayCollection($actions);
        $this->entityManager = $entityManager;
    }

    /**
     * @param $routePrefix
     * @param $entityName
     * @param ListViewProviderInterface $listViewProvider
     * @param AbstractType $formType
     * @param $view
     * @param array $actions
     *
     * @throws EntityNotFoundException
     * @return CrudController
     */
    public function get($entityName, $routePrefix = '', ListViewProviderInterface $listViewProvider = null, AbstractType $formType = null, $view = null, array $actions = [])
    {
        $repo = $this->entityManager->getRepository($entityName);

        if ($repo === null) {
            throw new EntityNotFoundException('CrudFactory: Invalid entity alias "' . $entityName . '"');
        }

        $dataProvider = new DataProvider($repo, $this->entityManager);
        $controller = new CrudController($dataProvider, $routePrefix, $listViewProvider, $formType, $view);

        if (empty($actions)) {
            $controller->setActions($this->actions);
        }

        return $controller;
    }
}
