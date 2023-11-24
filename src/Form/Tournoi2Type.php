<?php

namespace App\Form;

use App\Entity\Tournoi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\ORM\EntityManagerInterface;
class Tournoi2Type extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('nb_team', IntegerType::class, [
                'label' => 'Nombre de team',
                'attr' => [
                    'placeholder' => 'Saisissez le nombre de teams',
                    'min' => 0,
                    'max' => 100,
                    'step' => '2',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'required' => true,
                'data' => 4, // set default value to 4
            ])

            ->add('nb_joueur_team', IntegerType::class, [
                'label' => 'Nombre de joueurs',
                'attr' => [
                    'placeholder' => 'Saisissez le nombre de joueurs',
                    'min' => 2,
                    'max' => 100,
                    'step' => '2',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nombre de joueurs',
                    ])
                ],
            ])
            ->add('nomtournoi', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'attr' => [
                    'placeholder' => 'Saisissez le nom de l\'équipe',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'

                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom de l\'équipe',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le nom de l\'équipe doit comporter au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('device', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'attr' => [
                    'placeholder' => 'Saisissez le nom de l\'équipe',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'

                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom de l\'équipe',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le nom de l\'équipe doit comporter au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('datestart', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'style' => 'color:red;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'constraints' => [
                    new NotNull(),
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        $now = new \DateTimeImmutable();
                        $minDate = $now->modify('+2 days')->setTime(0, 0, 0);
                        if ($value < $minDate) {
                            $context->buildViolation('You can only select dates from 2 days after today.')
                                ->addViolation();
                        }
                    }),
                ],

            ])
            ->add('logos',FileType::class, [
                'label' => 'image du cours',

                'mapped' => false, //maneha maandi attribut esmo photo fl entity mte3na
                'required' => false,
                'attr'=>[
                    'placeholder' => 'Select a file',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
