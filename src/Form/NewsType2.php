<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\JeuxRepository;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;


class NewsType2 extends AbstractType
{
    private JeuxRepository $jeuxRepository;

    /**
     * @param JeuxRepository $jeuxRepository
     */
    public function __construct(JeuxRepository $jeuxRepository)
    {
        $this->jeuxRepository = $jeuxRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre',TextType::class, [
                'attr' => [
                    'class' => 'form-control col-md-5',
                    'style' => 'color: white;'
                ]
            ])
            ->add('idJeux', ChoiceType::class, [
                'choices' => $this->jeuxRepository->getJeuxChoices(),
                'choice_label' => 'nomGame',
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'color: white;'
                ]
            ])

            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'color: white;',
                ]
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

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
