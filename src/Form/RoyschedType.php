<?php
namespace App\Form;

use App\Entity\Pubs\Roysched;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoyschedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titleId', TextType::class, [
                'label' => 'Title ID',
                'disabled' => $options['is_edit'],
            ])
            ->add('lorange', IntegerType::class, [
                'required' => false,
                'label' => 'Low range',
            ])
            ->add('hirange', IntegerType::class, [
                'required' => false,
                'label' => 'High range',
            ])
            ->add('royalty', IntegerType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Roysched::class,
            'is_edit' => false,
        ]);
    }
}
