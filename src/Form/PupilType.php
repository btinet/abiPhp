<?php

namespace App\Form;

use App\Entity\Pupil;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PupilType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',TextType::class,[
                'attr'=> ['class' => 'form-control width-full','autofocus' => 'autofocus']
            ])
            ->add('lastname',TextType::class,[
                'attr'=> ['class' => 'form-control width-full']
            ])
            ->add('birthDate',DateType::class,[
                'attr'=> ['class' => 'form-control width-full'],
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('teacher',EntityType::class,[
                'class' => Teacher::class,
                'attr'=> ['class' => 'form-select width-full'],
                'required' => false
            ])
            ->add('examDate',IntegerType::class,[
                'attr'=> ['class' => 'form-control width-full'],
                'data' => date('Y')
            ])
            ->add('qualificationPoints',IntegerType::class,[
                'attr'=> ['class' => 'form-control width-full'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pupil::class,
        ]);
    }

}