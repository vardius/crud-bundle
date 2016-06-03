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

use Symfony\Component\HttpFoundation\Response;
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
    public function getOptions():array
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'route_suffix' => '',
            'pattern' => '',
            'template' => '',
            'defaults' => [
                '_format' => 'html'
            ],
            'requirements' => [
                '_format' => 'html|json|xml',
            ],
            'options' => [],
            'host' => '',
            'schemes' => [],
            'methods' => [],
            'condition' => '',
            'rest_route' => false,
            'checkAccess' => [],
        ]);

        $resolver->setAllowedTypes('route_suffix', 'string');
        $resolver->setAllowedTypes('pattern', 'string');
        $resolver->setAllowedTypes('template', 'string');
        $resolver->setAllowedTypes('defaults', 'array');
        $resolver->setAllowedTypes('requirements', 'array');
        $resolver->setAllowedTypes('options', 'array');
        $resolver->setAllowedTypes('host', 'string');
        $resolver->setAllowedTypes('schemes', 'array');
        $resolver->setAllowedTypes('methods', 'array');
        $resolver->setAllowedTypes('condition', 'string');
        $resolver->setAllowedTypes('rest_route', 'bool');
        $resolver->setAllowedTypes('checkAccess', 'array');
    }

    /**
     * Returns template name
     *
     * @return string
     */
    protected function getTemplate():string
    {
        return !empty($this->options['template']) ? $this->options['template'] : $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function checkRole(CrudController $controller, $data = null)
    {
        $role = $this->options['checkAccess'];
        if (!empty($role)) {

            $attributes = [];
            if (array_key_exists('attributes', $role)) {
                $attributes = $role['attributes'];
            }

            $message = null;
            if (array_key_exists('message', $role)) {
                $message = (string)$role['message'];
            }

            if (!empty($attributes)) {
                $controller->checkAccess($attributes, $data, $message);
            }
        }
    }
}
