<?php

namespace App\Form;

use App\Entity\Tournoi;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
class TournoiType extends AbstractType
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
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px;display:none'
                ],
                'required' => true,
                'data' => 4, // set default value to 4
            ])

            ->add('nb_joueur_team', IntegerType::class, [
                'label' => 'Nombre de joueurs',
                'attr' => [
                    'placeholder' => 'Saisissez le nombre de joueurs',
                    'min' => 0,
                    'max' => 100,
                    'step' => '2',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nombre de joueurs',
                    ]),
                    new GreaterThan([
                        'value' => 1,
                        'message' => 'Le nombre de joueurs doit être supérieur à zéro',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 2,
                        'message' => 'Le nombre de joueurs doit être supérieur ou égal à deux',
                    ]),
                ],
            ])
            ->add('nomtournoi', TextType::class, [
                'label' => 'Nom de tournoi',
                'attr' => [
                    'placeholder' => 'Saisissez le nom de tournoi',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'

                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom de tournoi',
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

                'data' => new \DateTimeImmutable(), // set default value
            ])

            ->add('logos', FileType::class, [
                'label' => 'image du cours',
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Select a file',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une image.',
                    ]),
                    new Image([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (PNG, JPG, JPEG, GIF, ou WebP).',
                        'maxSizeMessage' => 'La taille de l\'image ne doit pas dépasser {{ limit }}.',
                    ]),
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
