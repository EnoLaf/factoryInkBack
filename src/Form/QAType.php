<?php

namespace App\Form;

use App\Entity\QA;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class QAType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class,[
                'attr'=>['class'=>'form'],
                'label'=>'Question : ',
                'required'=>true
            ])
            ->add('answer', TextType::class,[
                'attr'=>['class'=>'form'],
                'label'=>'Réponse :',
                'required'=>true
            ])
            ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QA::class,
        ]);
    }
}
