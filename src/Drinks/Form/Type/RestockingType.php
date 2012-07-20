<?php
namespace Drinks\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Drinks\Form\Type\RestockingLineType;

/**
 * RestockingType class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class RestockingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userChoices = $this->prepareUserChoices($options['users']);

        $builder
            ->add('user_ids', 'choice', array('choices' => $userChoices, 'multiple' => true))
            ->add('amount', 'text')
            ->add('lines', 'collection', array(
                'type' => new RestockingLineType(),
                'allow_add' => true,
                'by_reference' => false,
                'options' => array('drinks' => $options['drinks'])
            ))
        ;
    }

    public function getName()
    {
        return 'restocking';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'users'  => array(),
            'drinks' => array(),
        ));
    }

    /**
     * @param $users
     * @return array
     */
    public function prepareUserChoices($users)
    {
        $choices = array();

        foreach ($users as $user) {
            $choices[$user->getId()] = $user->getName();
        }

        return $choices;
    }
}
