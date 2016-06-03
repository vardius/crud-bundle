<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Event;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Vardius\Bundle\CrudBundle\Controller\CrudController;
use Vardius\Bundle\CrudBundle\Data\DataProviderInterface;

/**
 * ActionEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ActionEvent
{
    /** @var  Request */
    protected $request;
    /** @var CrudController */
    protected $controller;

    /**
     * @param CrudController $controller
     * @param Request $request
     */
    function __construct(CrudController $controller, Request $request)
    {
        $this->controller = $controller;
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest():Request
    {
        return $this->request;
    }

    /**
     * @return CrudController
     */
    public function getController():CrudController
    {
        return $this->controller;
    }

    /**
     * @return AbstractType
     */
    public function getFormType():AbstractType
    {
        return $this->controller->getFormType();
    }

    /**
     * @return string
     */
    public function getView():string
    {
        return $this->controller->getView();
    }

    /**
     * @return DataProviderInterface
     */
    public function getDataProvider():DataProviderInterface
    {
        return $this->controller->getDataProvider();
    }
}
