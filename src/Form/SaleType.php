<?php
namespace App\Form;

use App\Entity\Pubs\Sale;
use App\Entity\Pubs\Store;
use App\Entity\Pubs\Title;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('store', EntityType::class, [
                'class' => Store::class,
                'choice_label' => fn(Store $store) => $store->getId() . ' - ' . ($store->getName() ?? ''),
                'disabled' => $options['is_edit'],
            ])
            ->add('ordNum', TextType::class, [
                'label' => 'Order #',
                'disabled' => $options['is_edit'],
            ])
            ->add('title', EntityType::class, [
                'class' => Title::class,
                'choice_label' => fn(Title $title) => $title->getId() . ' - ' . $title->getTitle(),
                'disabled' => $options['is_edit'],
            ])
            ->add('ordDate', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('qty', IntegerType::class)
            ->add('payterms', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
            'is_edit' => false,
        ]);
    }
}
