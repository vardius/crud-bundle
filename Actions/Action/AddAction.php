<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Actions\Action;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AddAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class AddAction extends SaveAction
{
    /**
     * Rest response success action code
     */
    CONST ACTION_CODE = 201;

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('methods', function (Options $options, $previousValue) {
            if ($options['rest_route']) {
                return ['POST'];
            }

            return $previousValue;
        });

        $resolver->setDefault('pattern', function (Options $options) {
            if ($options['rest_route']) {
                return '.{_format}';
            }

            return '/add.{_format}';
        });

        $resolver->setDefault('defaults', function (Options $options) {
            $format = $options['rest_route'] ? 'json' : 'html';

            return [
                '_format' => $format
            ];
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'add';
    }

}
