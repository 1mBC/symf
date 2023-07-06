<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use App\Entity\Choice;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\CriterionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($options['criteria'] as $criterion)
        {
            $builder->add('criterion_'.$criterion->getId(), CriterionType::class, [
                'user_account_id' => $options['user_account_id'],
                'data' => $criterion,
                'user_choices' => $options['user_choices'],
                'mapped' => false,
                'label' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
            'user_account_id' => null,
            'criteria' => null,
            'user_choices' => null,
        ]);
    }
}
