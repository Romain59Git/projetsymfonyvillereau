<?php
namespace App\Form;

use App\Entity\Rencontre;
use App\Entity\Licencie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RencontreTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('equipe', TextType::class, [
                'label' => 'Équipe',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Équipe A, Séniors, etc.'
                ]
            ])
            ->add('adversaire', TextType::class, [
                'label' => 'Adversaire',
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
            ])
            ->add('lieu', ChoiceType::class, [
                'label' => 'Lieu',
                'choices' => [
                    'Domicile' => 'Domicile',
                    'Extérieur' => 'Extérieur',
                ],
                'placeholder' => 'Sélectionnez un lieu',
            ])
            ->add('heure', TextType::class, [
                'label' => 'Heure',
            ])
            ->add('joueurs', EntityType::class, [
                'class' => Licencie::class,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Joueurs sélectionnés',
                'required' => false,
                'query_builder' => function(\App\Repository\LicencieRepository $repository) {
                    return $repository->createQueryBuilder('l')
                        ->orderBy('l.firstName', 'ASC')
                        ->addOrderBy('l.lastName', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rencontre::class,
        ]);
    }
} 