<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use phpDocumentor\Reflection\Types\False_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom',TextType::class,[
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('prix',IntegerType::class,[
                'constraints'=>[
                    new NotNull([
                        'message' => 'Le prix doit etre positif',
                    ]),
                    new GreaterThanOrEqual(['value'=>0, 'message' => 'Le prix doit être supérieur à 0']),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'saisir le prix du produit',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])
            ->add('quantite',IntegerType::class,[
                'constraints'=>[
                    new NotNull([
                        'message' => 'La quantite doit etre positif',
                    ]),
                    new GreaterThanOrEqual(['value'=>0, 'message' => 'La quantite doit être supérieur à 0']),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'saisir la quantite du produit',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])

            ->add('description',TextareaType::class,[
                'constraints'=>[
                    new NotNull([
                        'message' => 'saisir une description du produit',
                    ]),
                    new Length(['min'=>10,'max'=>1000])
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'saisir une description',
                    'style' => 'color:white;height:150px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
            ])

            ->add('imagep', FileType::class, [
                'label' => 'image du cours',
                'mapped' => false, //maneha maandi attribut esmo photo fl entity mte3na
                'required' => true,
                'attr' => [
                    'placeholder' => 'choisir une image',
                    'style' => 'color:white;height:65px;background-color:#22152c;width:100%;border: none;margin:0px 0px 10px;padding:24px 33px'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'choisir une image pour importer',
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
                        'mimeTypesMessage' => 'choisir une image valide (PNG, JPEG, jpg, GIF or WEBP)',
                    ]),
                ],
            ])
            ->add('idCategorie',EntityType::class,[  //foreign key
                'class' => Categorie::class,
                'choice_label'=>'nom',
                'multiple'=>False,
                'expanded'=>False,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
