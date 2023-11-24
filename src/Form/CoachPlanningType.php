<?php

namespace App\Form;

use App\Entity\Planning;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class CoachPlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('urlMeet',TextType::class,[
                'constraints'=>[
                    new NotNull([
                        'message' => 'Please select a title of the course',
                    ]),
                    new Length(['min'=>5,'max'=>255]),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter Url meet online coaching here',
                    'class' => 'form-control' // Add a class here
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}
