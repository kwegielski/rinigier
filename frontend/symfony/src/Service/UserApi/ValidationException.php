<?php

declare(strict_types=1);

namespace App\Service\UserApi;

use Exception;

class ValidationException extends Exception
{
    public array $errors;

    public function __construct(array $errors)
    {
        parent::__construct("Validation errors occurred");
        $this->errors = $errors;
    }
}
