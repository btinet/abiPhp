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
        $choices = [];
        if($pupil instanceof Pupil) {
            $exams = $pupil->getExams();
            for ($i = 1; $i <= 5; $i++){
                $hasNumber = false;
               foreach ($exams as $exam){
                   if($i == $exam->getExamNumber()) {
                       $hasNumber = true;
                   }
               }
               if(!$hasNumber){
                   $choices["$i. Prüfung"] = $i;
               }
            }
        }

        $builder
            ->add('examNumber',ChoiceType::class,[
                'attr'=> ['class' => 'form-select width-full','autofocus' => 'autofocus'],
                'choices' => $choices,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Prüfung wählen...',
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