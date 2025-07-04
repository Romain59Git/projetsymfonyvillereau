<?php

namespace App\Form;

use App\Entity\Licencie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LicencieTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['data'] && $options['data']->getId();

        $builder
            ->add('firstName', null, [
                'label' => 'Prénom'
            ])
            ->add('lastName', null, [
                'label' => 'Nom'
            ])
            ->add('email', null, [
                'label' => 'Email (utilisé pour le compte)',
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'choice',
                'required' => false,
                'years' => range(date('Y') - 80, date('Y')),
                'placeholder' => [
                    'year' => 'Année',
                    'month' => 'Mois', 
                    'day' => 'Jour'
                ],
                'format' => 'dd/MM/yyyy'
            ]);

        // Ajouter le champ mot de passe seulement lors de la création
        if (!$isEdit) {
            $builder->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Mot de passe provisoire',
                'help' => 'Un nouveau mot de passe devra être défini à la première connexion',
                'attr' => [
                    'autocomplete' => 'new-password',
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Licencie::class,
        ]);
    }
}
