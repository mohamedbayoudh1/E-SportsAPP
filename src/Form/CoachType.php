<?php

namespace App\Form;

use App\Entity\Coach;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CoachType extends AbstractType
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
                    'max'=>8,
                    'maxMessage'=>'max 8 caracteres',
                ])
            ],
            'attr' => [
                'placeholder' => 'Prenom',
            ],
        ])
        ->add('prixheure',null, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Prix',
            ],
        ])
        ->add('photoprofile',FileType::class, [
            'mapped' => false, 
            'required' => false,
            'label' => false,
        ])
        ->add('cv',FileType::class, [
            'mapped' => false,
            'required' => false,
            'label' => false,
        ])
        ->add('email',EmailType::class, [
            'label' => false,
            'constraints' => [
                new Email(['message' => 'Invalid email address.']),],
            'attr' => [
                'placeholder' => 'Mail',
            ],
        ])
        ->add('password',PasswordType::class, [
            'label' => false,
            'constraints'=>[
                new Length([
                    'min'=>8,
                    'minMessage'=>'min 8 caracteres',
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
                                $context->buildViolation('Please enter a valid phone number.')
                                    ->addViolation();
                            }
                        } catch (NumberParseException $e) {
                            $context->buildViolation('Please enter a valid phone number.')
                                ->addViolation();
                        }
                    },
                ]),
            ],
        ])
        ->add('date_naissance',DateType::class, [
            'label' => false,
            'widget' => 'single_text',
            'attr' => [
                'placeholder' => 'Code Recharge',
            ],
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
            'data_class' => Coach::class,
        ]);
    }
}
