<?php

namespace App\Form;

use App\Entity\Groupe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class GroupeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom_groupe', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le nom de groupe doit comporter au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('img', FileType::class, [
                'label' => 'image du cours',
                'mapped' => false, //maneha maandi attribut esmo photo fl entity mte3na
                'required' => true,
                'attr' => [
                    'placeholder' => 'selectioner une image ',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'selectioner une image ',
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
                        'mimeTypesMessage' => 'Il faut de type  (PNG, JPEG, jpg, GIF or WEBP)',
                    ]),
                ],
            ])

            ->add('description', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 16,
                        'minMessage' => 'La description doit comporter au moins {{ limit }} caractères.',
                    ]),
                ],
            ]);

    }

    // ...


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
        ]);
    }
}
