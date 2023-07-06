<?php

namespace App\Form;

use App\Entity\Choice;
use App\Entity\Criterion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\ChoiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CriterionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('choices', EntityType::class, [
            'class' => Choice::class,
            'choices' => $options['data']->getChoices(),
            'multiple' => true,
            'expanded' => true,
            'label' => $options['data']->getTitle(),
            'data' => $options['user_choices']->toArray(),
            'choice_attr' => function($choice, $key, $value) use($options) {
                return ['onchange' => 'saveChoice('.$options['user_account_id'].','.$choice->getId().',this)'];
            },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Criterion::class, // This form does not represent an entity
            'user_choices' => null,
            'user_account_id' => null,
        ]);
    }

}
