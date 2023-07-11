<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'attr' => ['class' => 'form'],
                'label' => 'Nom : ',
                'required' => true
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form'],
                'label' => 'Prénom : ',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form'],
                'label' => 'Mail : ',
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form'],
                'label' => 'Mot de passe : ',
                'required' => true
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'submitForm']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
