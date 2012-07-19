<?php

namespace Drinks\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserPasswordType class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class UserPasswordType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\Tests\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('password', 'password', array('constraints' => new Assert\NotBlank(array())))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user';
    }
}
