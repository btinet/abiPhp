<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAndPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username',TextType::class,[
                'attr' => ['class' => 'form-control width-full'],
            ])
            ->add('roles',ChoiceType::class,[
                'choices' => [
                    'Benutzer' => 'ROLE_USER',
                    'Administrator' => 'ROLE_ADMIN'
                ],
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'width-full form-control']
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password',
                    'hash_property_path' => 'password',
                    'attr'=> ['class' => 'form-control width-full']
                ],
                'first_name' => 'pass',
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr'=> ['class' => 'form-control width-full']
                ],
                'second_name' => 'repeat',
                'mapped' => false
            ])
            ->add('firstname',TextType::class,[
                'attr'=> ['class' => 'form-control width-full','autofocus' => 'autofocus']
            ])
            ->add('lastname',TextType::class,[
                'attr'=> ['class' => 'form-control width-full']
            ])
            ->add('email',TextType::class,[
                'attr'=> ['class' => 'form-control width-full']
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
