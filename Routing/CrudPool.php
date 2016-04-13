<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Vardius\Bundle\CrudBundle\Routing;


use Doctrine\Common\Collections\ArrayCollection;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * CrudPool
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudPool
{
    /** @var ArrayCollection */
    protected $controllers;

    /**
     * {@inheritdoc}
     */
    function __construct()
    {
        $this->controllers = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getControllers()
    {
        return $this->controllers;
    }

    /**
     * @param $id
     * @param CrudController $controller
     */
    public function addController($id, CrudController $controller)
    {
        $this->controllers->set($id, $controller);
    }

}
