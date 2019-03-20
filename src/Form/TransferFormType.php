<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 3/1/19
 * Time: 10:31 AM
 */

namespace App\Form;


use App\Entity\BankAccount;
use App\Entity\Transfer;
use App\Repository\BankAccountRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransferFormType extends AbstractType
{
    private $bankAccountRepository;
    private $tokenStorage;

    public function __construct(BankAccountRepository $bankAccountRepository, TokenStorageInterface $tokenStorage)
    {
        $this->bankAccountRepository = $bankAccountRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('senderAccountNumber', EntityType::class, [
                'class' => BankAccount::class,
                'choice_label' => function(BankAccount $bankAccount) {
                    return sprintf('%s (%s PLN)',
                        $bankAccount->getAccountNumber(),
                        number_format($bankAccount->getAvailableFunds(), 2, ',', ' '));
                },
                'choices' => $this->bankAccountRepository
                    ->getUserAccounts($this->tokenStorage->getToken()->getUser()->getId()),
                'mapped' => false
            ])
            ->add('receiverName')
            ->add('receiverAccountNumber', null, [
                'attr' => ['maxlength' => 32]
            ])
            ->add('title', TextareaType::class, [
                'attr' => ['class' => 'send-transfer-title'],
                'required' => false
            ])
            ->add('amount', NumberType::class, [
                'attr' => ['maxlength' => 11]
            ])
        ;
    }

    //Default translations file forms.en.yaml
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['transfer_domestic'],
            'translation_domain' => 'forms',
            'data_class' => Transfer::class
        ]);
    }
}