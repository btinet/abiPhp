<?php

namespace App\Form;

use App\Entity\ExamSubject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamSubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label',TextType::class,[
                'attr' => ['class' => 'form-control width-full','autofocus' => 'autofocus'],
            ])
            ->add('abbreviation',TextType::class,[
                'attr' => ['class' => 'form-control width-full'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamSubject::class,
        ]);
    }
}
