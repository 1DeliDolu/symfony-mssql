<?php
namespace App\Form;

use App\Entity\Pubs\PubInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PubInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pubId', TextType::class, [
                'label' => 'Publisher ID',
                'disabled' => $options['is_edit'],
            ])
            ->add('prInfo', TextareaType::class, [
                'label' => 'PR Info',
                'required' => false,
                'attr' => ['rows' => 6],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PubInfo::class,
            'is_edit' => false,
        ]);
    }
}
