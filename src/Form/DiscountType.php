<?php
namespace App\Form;

use App\Entity\Pubs\Discount;
use App\Entity\Pubs\Store;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discountType', TextType::class, [
                'label' => 'Discount type',
                'disabled' => $options['is_edit'],
            ])
            ->add('store', EntityType::class, [
                'class' => Store::class,
                'choice_label' => fn (?Store $store) => $store ? sprintf('%s (%s)', $store->getName(), $store->getId()) : '',
                'placeholder' => 'No store',
                'required' => false,
                'label' => 'Store',
            ])
            ->add('lowQty', IntegerType::class, [
                'required' => false,
                'label' => 'Low quantity',
            ])
            ->add('highQty', IntegerType::class, [
                'required' => false,
                'label' => 'High quantity',
            ])
            ->add('discount', NumberType::class, [
                'label' => 'Discount (%)',
                'scale' => 2,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discount::class,
            'is_edit' => false,
        ]);
    }
}

