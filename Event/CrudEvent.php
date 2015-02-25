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
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * CrudEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudEvent extends Event
{
    /**
     * @var EntityRepository
     */
    protected $source;

    /**
     * @var null|\Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    protected $form;

    /** @var CrudController */
    protected $controller;

    /**
     * @param EntityRepository $source
     * @param \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface|null $form
     * @param CrudController $controller
     */
    function __construct($source, CrudController $controller, $form = null)
    {
        $this->source = $source;
        $this->controller = $controller;
        $this->form = $form;
    }

    /**
     * @return EntityRepository
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return null|\Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return CrudController
     */
    public function getController()
    {
        return $this->controller;
    }

}
