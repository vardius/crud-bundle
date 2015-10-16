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
 * EditAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class EditAction extends SaveAction
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('requirements', ['id' => '\d+']);

        $resolver->setDefault('pattern', function (Options $options) {
            if ($options['rest_route']) {
                return '/{id}';
            }

            return '/edit/{id}';
        });

        $resolver->setDefault('methods', function (Options $options, $previousValue) {
            if ($options['rest_route']) {
                return ['PUT'];
            }

            return $previousValue;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'edit';
    }

}
