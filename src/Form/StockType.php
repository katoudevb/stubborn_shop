<?php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('size', ChoiceType::class, [
                'label' => 'Taille',
                'choices' => [
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La taille est obligatoire.'])
                ]
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Assert\Positive(['message' => 'La quantité doit être positive.'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stock::class
        ]);
    }
}
