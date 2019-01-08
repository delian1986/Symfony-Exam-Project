<?php

namespace ShopBundle\Form;

use ShopBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileEditForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("fullName")
            ->add("password", RepeatedType::class, [
                "type" => PasswordType::class
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => User::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'shop_bundle_user_profile_edit_form';
    }
}
