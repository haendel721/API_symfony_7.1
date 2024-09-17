<?php

namespace App\Form;

use App\Entity\Permission;
use App\Entity\Site;
use App\Entity\TypePermission;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Permission1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            // ->add('isAuthorized', ChoiceType::class, [
            //     // 'choices' => [
            //     //     'Authorized' => true,
            //     //     'Not Authorized' => false,
            //     // ],
            //     'expanded' => true,
            //     'multiple' => false,
            //     'mapped' => false,
            // ])
            // ->add('typePermission', EntityType::class, [
            //     'class' => TypePermission::class,
            //     'choice_label' => 'name',
            // ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permission::class,
            'csrf_protection'=> true // Protection CSRF activée
        ]);
    }
}
