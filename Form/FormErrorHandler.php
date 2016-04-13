<?php
/**
 * This file is part of the tactic-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FormErrorHandler
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FormErrorHandler
{
    /** @var  TranslatorInterface */
    protected $translator;

    /**
     * FormErrorHandler constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns an array with form fields errors
     *
     * @param FormInterface $form
     * @param bool|false $useLabels
     * @param array $errors
     * @return array
     */
    public function getErrorMessages(FormInterface $form, $useLabels = false, $errors = array())
    {
        if ($form->count() > 0) {
            foreach ($form->all() as $child) {
                if (!$child->isValid()) {
                    $errors = $this->getErrorMessages($child, $useLabels, $errors);
                }
            }
        }

        foreach ($form->getErrors() as $error) {
            if ($useLabels) {
                $fieldNameData = $this->getErrorFormLabel($form);
            } else {
                $fieldNameData = $this->getErrorFormId($form);
            }

            $fieldName = $fieldNameData;

            if ($useLabels) {
                /**
                 * @ignore
                 */
                $fieldName = $this->translator->trans($fieldNameData['label'], array(), $fieldNameData['domain']);
            }

            $errors[$fieldName] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * Returns first label for field with error
     *
     * @param  FormInterface $form
     * @return array
     */
    protected function getErrorFormLabel(FormInterface $form)
    {
        $vars = $form->createView()->vars;

        $label = $vars['label'];
        $translationDomain = $vars['translation_domain'];

        $result = array(
            'label' => $label,
            'domain' => $translationDomain,
        );

        if (empty($label)) {
            if ($form->getParent() !== null) {
                $result = $this->getErrorFormLabel($form->getParent());
            }
        }

        return $result;
    }

    /**
     * Returns ID for field with error
     *
     * @param  FormInterface $form
     * @return string
     */
    protected function getErrorFormId(FormInterface $form)
    {
        $vars = $form->createView()->vars;
        return $vars['id'];
    }
}