<?php
namespace App\Form;

use App\Entity\Pubs\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class, [
                'label' => 'Author ID',
                'disabled' => $options['is_edit'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last name',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First name',
            ])
            ->add('phone', TextType::class, [
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'required' => false,
            ])
            ->add('state', TextType::class, [
                'required' => false,
            ])
            ->add('zip', TextType::class, [
                'required' => false,
                'label' => 'ZIP',
            ])
            ->add('contract', CheckboxType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
            'is_edit' => false,
        ]);
    }
}

