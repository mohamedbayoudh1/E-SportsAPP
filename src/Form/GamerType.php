<?php

namespace App\Form;

use App\Entity\Gamer;
use Doctrine\DBAL\Types\FloatType;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType ;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class GamerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null, [
                'label' => false,
                'constraints'=>[
                    new Length([
                        'max'=>8,
                        'maxMessage'=>'max 8 caracteres',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('prenom',null, [
                'label' => false,
                'constraints'=>[
                    new Length([
                        'max'=>20,
                        'maxMessage'=>'maximum 20 caracteres',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Prenom',
                ],
            ])
            ->add('tag',null, [
                'label' => false,
                'constraints'=>[
                    new Length([
                        'max'=>8,
                        'maxMessage'=>'maximum 8 caracteres',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Pseudo',
                ],
            ])
            ->add('photoprofile',FileType::class, [
                'mapped' => false, //maneha maandi attribut esmo photo fl entity mte3na
                'required' => false,
                'label' => false,
            ])
            ->add('email',EmailType::class, [
                'label' => false,
                'constraints' => [
                    new Email(['message' => 'ce n\' est pas une adresse email']),],
                'attr' => [
                    'placeholder' => 'Mail',
                ],
            ])
            ->add('password',PasswordType::class, [
                'label' => false,
                'constraints'=>[
                    new Length([
                        'min'=>8,
                        'minMessage'=>'minimum 8 caracteres',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Password',
                ],
            ])
            ->add('phone', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'phone number',
                ],
                'constraints' => [
                    new Callback([
                        'callback' => function ($phone, ExecutionContextInterface $context) {
                            $phoneUtil = PhoneNumberUtil::getInstance();
                            try {
                                $phoneNumber = $phoneUtil->parse($phone, 'TN');
                                if (!$phoneUtil->isValidNumber($phoneNumber)) {
                                    $context->buildViolation('Ce n\' est pas un numero tunisian')
                                        ->addViolation();
                                }
                            } catch (NumberParseException $e) {
                                $context->buildViolation('Ce n\' est pas un numero de telephone')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ])
            ->add('date_naissance',DateType::class, [
                'label' => false,
                'widget' => 'single_text',
            ])
            ->add('about',TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Parlez-nous de vous',
                    'rows' => 3
                ],
            ])
            ->add('Enregistrer',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gamer::class,
        ]);
    }
}
