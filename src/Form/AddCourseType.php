<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Jeux;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class AddCourseType extends AbstractType
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
                    'placeholder' => 'Enter Course title here',
                    'class' => 'form-control' // Add a class here
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
            ->add('picture', FileType::class, [
                'label' => 'image du cours',
                'mapped' => false, //maneha maandi attribut esmo photo fl entity mte3na
                'required' => true,
                'attr' => [
                    'placeholder' => 'Select a file',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Please select a picture to upload',
                    ]),
                    new Image([
                        'maxSize' => '6M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (PNG, JPEG, jpg, GIF or WEBP)',
                    ]),
                ],
            ])
            ->add('videoC', FileType::class, [
                'label' => 'video du cours',
                'mapped' => false, //maneha maandi attribut esmo photo fl entity mte3na
                'required' => true,
                'attr' => [
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Please select a video to upload',
                    ]),
                    new File([
                        'maxSize' => '25M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/mpeg',
                            'video/ogg',
                            'video/webm',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid video file (MP4, MPEG, OGG or WEBM)',
                    ]),
                ],
            ])
            ->add('prix',IntegerType::class,[
                'constraints'=>[
                    new NotNull([
                        'message' => 'Please select a price of the course',
                    ]),
                    new GreaterThanOrEqual(['value'=>0, 'message' => 'Le prix doit être supérieur à 0']),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter Course Price here',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])
            ->add('niveau',ChoiceType::class,[
                'choices' => [
                    'Débutant' => 'debutant',
                    'Intermédiaire' => 'intermediaire',
                    'Avancé' => 'avance',
                ],
                'constraints' => [
                    new Choice(['choices' => ['debutant', 'intermediaire', 'avance']]),
                ],
                'required' => true,
                'attr' => [
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])
            ->add('idJeux', EntityType::class, [
                'required' => true,
                'class' => Jeux::class,
                'choice_label' => 'nom_game',
                'attr' => [
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
            'csrf_protection' => false,
        ]);
    }
}
