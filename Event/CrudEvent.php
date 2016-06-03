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

use Symfony\Component\EventDispatcher\Event;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * CrudEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudEvent extends Event
{
    /** @var mixed */
    protected $source;
    /** @var mixed */
    protected $data;
    /** @var CrudController */
    protected $controller;

    /**
     * @param mixed $source
     * @param CrudController $controller
     * @param mixed $data
     */
    function __construct($source, CrudController $controller, $data = null)
    {
        $this->source = $source;
        $this->controller = $controller;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     * @return CrudEvent
     */
    public function setSource($source):self
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param mixed $data
     * @return CrudEvent
     */
    public function setData($data):self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return CrudController
     */
    public function getController():CrudController
    {
        return $this->controller;
    }
}
