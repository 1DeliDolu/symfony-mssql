<?php
namespace App\Form;

use App\Entity\Pubs\TitleAuthor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitleAuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('auId', TextType::class, [
                'label' => 'Author ID',
                'disabled' => $options['is_edit'],
            ])
            ->add('titleId', TextType::class, [
                'label' => 'Title ID',
                'disabled' => $options['is_edit'],
            ])
            ->add('auOrd', IntegerType::class, [
                'required' => false,
                'label' => 'Order',
            ])
            ->add('royaltyper', IntegerType::class, [
                'required' => false,
                'label' => 'Royalty %',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TitleAuthor::class,
            'is_edit' => false,
        ]);
    }
}
