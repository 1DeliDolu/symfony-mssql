<?php
namespace App\Form;

use App\Entity\Pubs\Title;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class, [
                'label' => 'Title ID',
                'disabled' => $options['is_edit'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Name',
            ])
            ->add('type', TextType::class, [
                'label' => 'Type',
            ])
            ->add('pubId', TextType::class, [
                'label' => 'Publisher ID',
                'required' => false,
            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'scale' => 2,
            ])
            ->add('advance', NumberType::class, [
                'required' => false,
                'scale' => 2,
            ])
            ->add('royalty', NumberType::class, [
                'required' => false,
            ])
            ->add('ytdSales', NumberType::class, [
                'required' => false,
            ])
            ->add('notes', TextType::class, [
                'required' => false,
            ])
            ->add('pubdate', DateType::class, [
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Title::class,
            'is_edit' => false,
        ]);
    }
}
