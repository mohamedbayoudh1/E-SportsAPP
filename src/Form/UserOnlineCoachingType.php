<?php

namespace App\Form;

use App\Entity\Coach;
use App\Entity\CoachSkills;
use App\Entity\Gamer;
use App\Entity\Jeux;
use App\Entity\Planning;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class UserOnlineCoachingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre',TextType::class,[
                'constraints'=>[
                    new NotNull([
                        'message' => 'Please select a title of the course',
                    ]),
                    new Length(['min'=>5,'max'=>255]),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter Coaching title here',
                    'class' => 'form-control' // Add a class here
                ],
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => new NotNull(),
                'data' => new \DateTimeImmutable(), // set default value
                'attr' => [
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])
            ->add('description',TextareaType::class,[
                'constraints'=>[
                    new NotNull([
                        'message' => 'Please select a description of the course',
                    ]),
                    new Length(['min'=>10,'max'=>1000])
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter Course Description here',
                    'style' => 'color:white;height:150px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])
            ->add('nbreHeureSeance',ChoiceType::class,[
                'choices' => [
                    '1h' => 1,
                    '2h' => 2,
                    '3h' => 3,
                ],
                'constraints' => [
                    new Choice(['choices' => [1, 2, 3]]),
                ],
                'required' => true,
                'attr' => [
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],

            ])
            ->add('coachSkills', EntityType::class, [
                'class' => CoachSkills::class,
                'choices' => $options['coach_skills'],
                'choice_label' => 'Jeux.nomgame',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])

        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
            'coach_skills' => null,
        ]);
    }
}
