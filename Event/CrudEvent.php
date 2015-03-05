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

use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * CrudEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudEvent extends Event
{
    /** @var EntityRepository */
    protected $source;
    /** @var FormInterface|mixed */
    protected $data;
    /** @var CrudController */
    protected $controller;

    /**
     * @param EntityRepository $source
     * @param CrudController $controller
     * @param FormInterface|mixed $data
     */
    function __construct($source, CrudController $controller, $data = null)
    {
        $this->source = $source;
        $this->controller = $controller;
        $this->data = $data;
    }

    /**
     * @return EntityRepository
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return FormInterface|mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return CrudController
     */
    public function getController()
    {
        return $this->controller;
    }

}
