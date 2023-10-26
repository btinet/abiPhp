<?php

namespace App\Form;

use App\Entity\Exam;
use App\Entity\ExamSubject;
use App\Entity\Pupil;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $pupil = $options['custom_option'];
        if($pupil instanceof Pupil) {

        }
        $builder
            ->add('examNumber',ChoiceType::class,[
                'attr'=> ['class' => 'form-select width-full'],
                'choices' => [
                    '1. Leistungskurs' => 1,
                    '2. Leistungskurs' => 2,
                    '3. Grundkurs' => 3,
                    '4. Grundkurs (mündlich)' => 4,
                    '5. Prüfungskomponente' => 5,
                ],
                'multiple' => false,
                'expanded' => false,
                'placeholder' => '',
            ])
            ->add('subject',EntityType::class,[
                'empty_data' => '',
                'class' => ExamSubject::class,
                'attr'=> ['class' => 'form-select width-full'],
                'placeholder' => '',
            ])
            ->add('examPoints',IntegerType::class,[
                'attr'=> ['class' => 'form-control width-full'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exam::class,
            'custom_option' => null,
        ]);
    }

}