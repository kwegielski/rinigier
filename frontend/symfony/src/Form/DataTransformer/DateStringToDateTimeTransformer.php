<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateStringToDateTimeTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if (!$value) {
            return null;
        }

        if (is_string($value)) {
            return new \DateTimeImmutable($value);
        }

        return $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        throw new TransformationFailedException('Expected a DateTimeInterface.');
    }
}
