<?php

namespace App\Form;

use App\Entity\Tattoo;
use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TattooType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', FileType::class, [
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
            ->add('flash', ChoiceType::class,
            [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'attr'=>['class'=>'form'],
                'label' => 'Flash?',
                'required' => true
            ])
            ->add('price', NumberType::class,
            [
                'attr'=>['class'=>'form'],
                'label' => 'Prix :',
                'required' => false
            ])
            ->add('artist', EntityType::class,
            [
                // looks for choices from this entity
                'class' => Artist::class,
                'label' => 'Artiste :',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'required' => false
            ])
            ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tattoo::class,
        ]);
    }
}
