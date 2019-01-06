<?php

namespace ShopBundle\Form;

use function PHPSTORM_META\type;
use ShopBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name")
            ->add("category", null, [
                "placeholder" => "Select category"
            ])
            ->add("description")
            ->add("image", FileType::class, [
                'data_class' => null,
                'required' => true
            ])
            ->add("quantity")
            ->add("price", MoneyType::class)
            ->add("isListed",  ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false
                ],
                'label' => 'Is product listed in the shop?',
                'required' => true])
            ->add("promotions", EntityType::class, [
                "class" => 'ShopBundle\Entity\Promotion',
                "multiple" => true,
                "expanded" => true
            ]);
        ;

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ShopBundle\Entity\Product'
        ));
    }

}
