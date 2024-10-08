<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use App\Entity\CategorySite;
// use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextType; 

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('url')
            ->add('categorySite', EntityType::class, [
                'class' => CategorySite::class,
                'choice_label' => 'name', // Remplacez 'name' par une propriété de votre entité qui est une chaîne
            ])
            ->add('id_login')
            ->add('class_login')
            ->add('id_mdp')
            ->add('class_mdp')
            ->add('class_mdp')
            ->add('class_submit')
            
            // ->add('login', TextType::class, [
            //     'required' => true,
            // ])
            // ->add('password', PasswordType::class, [
            //     'required' => true,
            // ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
            'csrf_protection'=> true // Protection CSRF activée
        ]);
    }
}
