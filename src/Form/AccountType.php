<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use App\Entity\Choice;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\ChoiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if(isset($options['create'])){
            $builder
            ->add('pseudo')
            ->add('password')
            ;
        }
        if(isset($options['section'])){
            $builder
            ->add('choices', EntityType::class, [
                'class' => Choice::class,
                'query_builder' => function (ChoiceRepository $cr) use($options) {
                    return $cr->createQueryBuilder('c')
                        ->join('c.criterion', 'cr')
                        ->where('cr.section = :section')
                        ->setParameter('section', $options['section']);
                },
                'multiple' => true,
                'expanded' => true, // change this to false if you want a select dropdown instead of checkboxes
                'label' => 'section' . $options['section'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
            'section' => null,
        ]);
    }
}
