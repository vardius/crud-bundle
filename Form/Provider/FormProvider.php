<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Form\Provider;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactory;

/**
 * FormProvider
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FormProvider
{

    /** @var  EntityManager */
    protected $entityManager;
    /** @var FormFactory */
    protected $formFactory;

    /**
     * @param EntityManager $entityManager
     * @param FormFactory $formFactory
     */
    function __construct(EntityManager $entityManager, FormFactory $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    /**
     * @param AbstractType $formType
     * @param null $data
     * @param array $options
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function createForm(AbstractType $formType = null, $data = null, array $options = array())
    {
        return $this->formFactory->create(($formType ? $formType : 'form'), $data, $options);
    }
}
