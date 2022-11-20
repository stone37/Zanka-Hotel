<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute] class UserNotPartner extends Constraint
{
    public string $message = 'Cet utilisateur est un partenaire.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}