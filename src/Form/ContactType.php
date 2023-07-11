<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form'],
                'label' => 'Nom : ',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form'],
                'label' => 'Mail : ',
                'required' => true
            ])
            ->add('phone', IntegerType::class, [
                'attr' => ['class' => 'form'],
                'label' => 'Téléphone : '
            ])
            ->add('content', TextType::class, [
                'attr' => ['class' => 'form'],
                'label' => 'Message : ',
                'required' => true
            ])
            ->add('file', FileType::class, [
                'attr' => ['class' => 'submitForm'],
                'label' => 'Joindre un fichier',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Merci de joindre un fichier .png ou .jpeg',
                    ])
                ]
            ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
