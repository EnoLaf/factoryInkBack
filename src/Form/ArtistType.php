<?php

namespace App\Form;

use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr'=>['class'=>'form'],
                'label'=>'Nom : ',
                'required'=>true
            ])
            ->add('style', TextType::class,[
                'attr'=>['class'=>'form'],
                'label'=>'Syle : ',
                'required'=>true
            ])
            ->add('resume', TextareaType::class,[
                'attr'=>['class'=>'form'],
                'label'=>'Présentation : ',
                'required'=>true
            ])
            ->add('profilePicture', FileType::class, [
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
            ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
