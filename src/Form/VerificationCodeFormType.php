<?php

namespace App\Form;

use App\Entity\Transfer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VerificationCodeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('verificationCode', NumberType::class, [
                'attr' => ['maxlength' => 6],
                'invalid_message' => 'Wprowadź poprawny kod'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => 'verification_code',
            'translation_domain' => 'forms',
            'data_class' => Transfer::class,
        ]);
    }
}
