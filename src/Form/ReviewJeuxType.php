<?php

namespace App\Form;

use App\Entity\ReviewJeux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewJeuxType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'label' => 'Rating',
                'choices' => [
                    '5',
                    '4',
                    '3',
                    '2',
                    '1',
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'star-rating'
                ]
            ])


            ->add('submit', SubmitType::class);

    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReviewJeux::class,
        ]);
    }
}
