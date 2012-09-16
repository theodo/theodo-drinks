<?php

namespace Theodo\DrinksBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * DrinkType class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class DrinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('purchasePrice', 'integer')
            ->add('salePrice', 'integer')
            ->add('quantity', 'integer');
    }

    public function getName()
    {
        return 'drink';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            "data_class" => 'Theodo\DrinksBundle\Document\Drink'
        ));
    }
}
