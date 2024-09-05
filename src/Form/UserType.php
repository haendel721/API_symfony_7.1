<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email')
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Admin' => 'ROLE_ADMIN',
                'User' => 'ROLE_USER',
            ],
            'multiple' => true,
            'expanded' => true, // Affiche des cases à cocher (checkboxes)
        ])
        ->add('password', PasswordType::class, [
            'label' => 'New Password',
            'required' => false, // Ne pas rendre obligatoire lors de la modification si ce n'est pas requis
        ])
        ->add('name')
        ->add('surname')
        ->add('dateNaissance', null, [
            'widget' => 'single_text',
        ])
        ->add('lot')
        ->add('tel')
        ->add('image', FileType::class, [
            'label' => 'Upload Image',
            'required' => false,
            'mapped' => false, // Si l'image n'est pas directement mappée à une propriété de l'entité
        ])
        ->add('situationFamiliale')
        ->add('lieuNaissance')
        ->add('genre')
        ->add('fonction');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
