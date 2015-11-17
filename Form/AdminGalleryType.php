<?php

namespace Istorm\AdminExtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AdminGalleryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefined(array('base_path'));

        $resolver->setDefaults(array(
            'base_path' => '',
            'required' => false,
        ));

        //$resolver->setAllowedTypes('base_path','string');

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('base_path', $options)) {
            $view->vars['base_path'] = $options['base_path'];
        }
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'admingallery';
    }
}