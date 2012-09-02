<?php
namespace Drinks\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * RestockingLineType class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class RestockingLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $drinkChoices = $this->prepareDrinkChoices($options['drinks']);

        $builder
            ->add('drink_id', 'choice', array('choices' => $drinkChoices, 'expanded' => true))
            ->add('quantity', 'number');
    }

    public function getName()
    {
        return 'restocking_line';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\\Drinks\\Document\\RestockingLine',
            'drinks' => array(),
        ));
    }

    public function prepareDrinkChoices($drinks)
    {
        $choices = array();

        foreach ($drinks as $drink) {
            $choices[$drink->getId()] = $drink->getName();
        }

        return $choices;
    }
}
