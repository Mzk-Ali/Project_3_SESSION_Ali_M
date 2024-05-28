<?php

namespace App\Form;

use App\Entity\Lecon;
use App\Entity\Session;
use App\Entity\Programme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duree', IntegerType::class)
            ->add('session', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'id',
            ])
            ->add('lecon', EntityType::class, [
                'class' => Lecon::class,
                // 'choice_label' => 'nomLecon',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
}
