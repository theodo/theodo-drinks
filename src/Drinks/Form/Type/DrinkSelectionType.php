<?php

namespace Drinks\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * DrinkSelectionType class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class DrinkSelectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_id', 'hidden')
            ->add('drink_id', 'choice', array('choices' => $options['drink_choices'], 'expanded' => true))
            ->add('payment', 'hidden');
    }

    public function getName()
    {
        return 'selection';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'drink_choices' => array()
        ));
    }
}
