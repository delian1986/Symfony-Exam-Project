<?php

namespace ShopBundle\Form;

use ShopBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAdminEditForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("email")
            ->add("fullName")
            ->add("balance")
            ->add("sroles", EntityType::class, [
                "class" => 'ShopBundle\Entity\Role',
                "multiple" => true,
                "expanded" => true
            ])
            ->add("isActive", ChoiceType::class, [
                'choices' => [
                    'choice_label' => 'name',
                    'No' => false,
                    'Yes' => true
                ],
                'label' => 'Is user account active?',
                'required' => true]
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => User::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'shop_bundle_user_admin_edit_form';
    }
}
