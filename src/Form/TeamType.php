<?php

namespace App\Form;

use App\Entity\Team;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_team', TextType::class, [
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
            ->add('nb_joueurs', IntegerType::class, [
                'label' => 'Nombre de joueurs',
                'attr' => [
                    'placeholder' => 'Saisissez le nombre de joueurs',
                    'min' => 2,
                    'max' => 100,
                    'step' => '1',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nombre de joueurs',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 2,
                        'message' => 'Le nombre de joueurs doit être supérieur ou égal à 2.',
                    ])
                ],
            ])

            ->add('about', TextareaType::class, [
                'label' => 'Description de l\'équipe',
                'attr' => [
                    'placeholder' => 'Saisissez une description de l\'équipe (100 caractères maximum)',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'

                ],
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 100,
                        'maxMessage' => 'La description de l\'équipe ne doit pas dépasser {{ limit }} caractères',
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez saisir description',
                    ])
                ],
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
            'data_class' => Team::class,
        ]);
    }
}
