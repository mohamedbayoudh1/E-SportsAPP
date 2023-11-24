<?php

namespace App\Form;

use App\Entity\Jeux;


use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;


class JeuxType2 extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomGame', TextType::class, [
                'label' => 'Nom du jeu',
                'attr' => [
                    'class' => 'form-control col-md-5',
                    'novalidate' => true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "nom du jeu" est obligatoire',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le nom du jeu doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom du jeu ne doit pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('maxPlayers', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuillez saisir une valeur',
                    ]),
                    new Type([
                        'type' => 'integer',
                        'message' => 'veuillez saisir un numero',
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => 50,
                        'notInRangeMessage' => 'Le nombre des joueurs est entre {{ min }} et {{ max }}',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => true
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'minMessage' => 'The description must be at least {{ limit }} characters long',
                        'maxMessage' => 'The description cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('picture',FileType::class, [
                'label' => 'Image',
                'mapped' => false, //maneha maandi attribut esmo photo fl entity mte3na
                'required' => false,
                'attr'=>[
                    'placeholder' => 'Choisir une image',
                    'class' => 'form-control file-upload-info',
                    'novalidate' => true
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Image non valide (JPEG, PNG, GIF).',
                    ])
                ]
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary me-2',
                    'style' => '    margin: 1rem;min-width: -webkit-fill-available;'
                ]
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Jeux::class,
        ]);
    }
}
