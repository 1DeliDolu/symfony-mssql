<?php
namespace App\Form;

use App\Entity\Pubs\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', IntegerType::class, [
                'label' => 'Job ID',
                'disabled' => $options['is_edit'],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('minLvl', IntegerType::class, [
                'label' => 'Min level',
                'attr' => ['min' => 10, 'max' => 250],
                'help' => 'Allowed range: 10 - 250',
            ])
            ->add('maxLvl', IntegerType::class, [
                'label' => 'Max level',
                'attr' => ['min' => 10, 'max' => 250],
                'help' => 'Must be ≥ min level (10 - 250)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
            'is_edit' => false,
        ]);
    }
}
