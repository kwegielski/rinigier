<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\DataTransformer\DateStringToDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserFilterFormType extends AbstractType
{
    public function __construct(
        private readonly DateStringToDateTimeTransformer $dateTransformer
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, [
                'required' => false
            ])
            ->add('last_name', TextType::class, [
                'required' => false
            ])
            ->add('gender', ChoiceType::class, [
                'required' => false,
                'choices' => ['Male' => 'male', 'Female' => 'female'],
                'placeholder' => '—'
            ])
            ->add('birthdate_from', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('birthdate_to', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
        ;

        $builder->get('birthdate_from')->addModelTransformer($this->dateTransformer);
        $builder->get('birthdate_to')->addModelTransformer($this->dateTransformer);
    }
}
