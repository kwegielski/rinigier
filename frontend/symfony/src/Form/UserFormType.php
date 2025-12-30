<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\DataTransformer\DateStringToDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class UserFormType extends AbstractType
{
    public function __construct(
        private readonly DateStringToDateTimeTransformer $dateTransformer
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name')
            ->add('last_name')
            ->add('gender', ChoiceType::class, [
                'choices' => ['Male' => 'male', 'Female' => 'female']
            ])
            ->add('birthdate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => true,
            ])
        ;

        $builder->get('birthdate')->addModelTransformer($this->dateTransformer);
    }
}
