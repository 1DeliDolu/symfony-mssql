<?php
namespace App\Form;

use App\Entity\Pubs\Employee;
use App\Entity\Pubs\Job;
use App\Entity\Pubs\Publisher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('empId', TextType::class, [
                'label' => 'Employee ID',
                'disabled' => $options['is_edit'],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First name',
            ])
            ->add('middleInitial', TextType::class, [
                'required' => false,
                'label' => 'Middle initial',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last name',
            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => fn(?Job $job) => $job ? $job->getDescription() : '',
                'label' => 'Job',
            ])
            ->add('jobLevel', IntegerType::class, [
                'label' => 'Job level',
            ])
            ->add('publisher', EntityType::class, [
                'class' => Publisher::class,
                'choice_label' => fn(?Publisher $pub) => $pub ? ($pub->getName() ?? $pub->getId()) : '',
                'label' => 'Publisher',
            ])
            ->add('hireDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Hire date',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
            'is_edit' => false,
        ]);
    }
}
