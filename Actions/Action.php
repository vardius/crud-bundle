<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Actions;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\CrudBundle\Controller\CrudController;

/**
 * Action
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class Action implements ActionInterface
{
    /** @var array */
    protected $options;

    /**
     * @inheritDoc
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'route_suffix' => '',
                'pattern' => '',
                'template' => '',
                'defaults' => [],
                'requirements' => [],
                'options' => [],
                'host' => '',
                'schemes' => [],
                'methods' => [],
                'condition' => '',
                'rest_route' => false,
                'response_type' => 'html',
            )
        );
        $resolver->setAllowedTypes(
            array(
                'route_suffix' => 'string',
                'pattern' => 'string',
                'template' => 'string',
                'defaults' => 'array',
                'requirements' => 'array',
                'options' => 'array',
                'host' => 'string',
                'schemes' => 'array',
                'methods' => 'array',
                'condition' => 'string',
                'rest_route' => 'bool',
                'response_type' => 'string',
            )
        );
        $resolver->setAllowedValues('response_type', array('html', 'xml', 'json'));
    }

    /**
     * Returns template name
     *
     * @return bool
     */
    protected function getTemplate()
    {
        return !empty($this->options['template']) ? $this->options['template'] : $this->getName();
    }

    /**
     * Returns response handler class
     *
     * @param CrudController $controller
     * @return \Vardius\Bundle\CrudBundle\Response\ResponseHandler
     */
    protected function getResponseHandler(CrudController $controller)
    {
        return $controller->get('vardius_crud.response.handler');
    }

}
