<?php

namespace Drinks\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->add('user_id', 'choice', array('choices' => $options['data']['userChoices']))
            ->add('drink_id', 'choice', array('choices' => $options['data']['drinkChoices'], 'expanded' => true))
            ->add('payment', 'hidden');
    }

    public function getName()
    {
        return 'selection';
    }
}
